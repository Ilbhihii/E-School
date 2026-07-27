import 'dart:convert';

import 'package:flutter/foundation.dart';

import '../config/api_config.dart';
import '../models/user.dart';
import 'api_client.dart';
import 'storage_service.dart';

class AuthService extends ChangeNotifier {
  final ApiClient _api = ApiClient();
  late final StorageService _storage;

  User? _currentUser;
  bool _isLoading = false;
  String? _errorMessage;

  User? get currentUser => _currentUser;
  bool get isLoading => _isLoading;
  bool get isAuthenticated => _currentUser != null && _api.hasToken;
  String? get errorMessage => _errorMessage;
  String? get userToken => _storage.getToken() as String?; // will be set after storage init

  // ─── Initialisation (appelée au démarrage) ───
  Future<void> init() async {
    _storage = await StorageService.getInstance();
    final token = await _storage.getToken();
    if (token != null && token.isNotEmpty) {
      _api.setToken(token);
      final userData = _storage.getUserData();
      if (userData != null) {
        try {
          _currentUser = User.fromJson(jsonDecode(userData));
          notifyListeners();
        } catch (_) {}
      }
      // Rafraîchir le profil en arrière-plan
      try {
        await fetchProfile();
      } catch (_) {}
    }
  }

  // ─── Connexion ───
  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final response = await _api.post(ApiConfig.login, data: {
      'email': email,
      'password': password,
    });

    if (response.success && response.data != null) {
      final token = response.data['token'] as String?;
      final userJson = response.data['user'] as Map<String, dynamic>?;

      if (token != null && userJson != null) {
        _api.setToken(token);
        await _storage.saveToken(token);
        _currentUser = User.fromJson(userJson);
        await _storage.saveUserData(jsonEncode(userJson));
        _isLoading = false;
        notifyListeners();
        return true;
      }
    }

    _errorMessage = response.message ?? 'Email ou mot de passe incorrect.';
    _isLoading = false;
    notifyListeners();
    return false;
  }

  // ─── Inscription ───
  Future<bool> register({
    required String name,
    required String email,
    required String password,
    String passwordConfirmation = '',
    String role = 'student',
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final response = await _api.post(ApiConfig.register, data: {
      'name': name,
      'email': email,
      'password': password,
      'password_confirmation': passwordConfirmation.isEmpty ? password : passwordConfirmation,
      'role': role,
    });

    if (response.success && response.data != null) {
      final token = response.data['token'] as String?;
      final userJson = response.data['user'] as Map<String, dynamic>?;

      if (token != null && userJson != null) {
        _api.setToken(token);
        await _storage.saveToken(token);
        _currentUser = User.fromJson(userJson);
        await _storage.saveUserData(jsonEncode(userJson));
        _isLoading = false;
        notifyListeners();
        return true;
      }
    }

    _errorMessage = response.message ?? 'Erreur lors de l\'inscription.';
    _isLoading = false;
    notifyListeners();
    return false;
  }

  // ─── Déconnexion ───
  Future<void> logout() async {
    await _api.post(ApiConfig.logout);
    _api.clearToken();
    _currentUser = null;
    await _storage.clearAll();
    notifyListeners();
  }

  // ─── Récupération du profil ───
  Future<bool> fetchProfile() async {
    final response = await _api.get(ApiConfig.profile);
    if (response.success && response.data != null) {
      final userJson = response.data as Map<String, dynamic>?;
      if (userJson != null) {
        _currentUser = User.fromJson(userJson);
        await _storage.saveUserData(jsonEncode(userJson));
        notifyListeners();
        return true;
      }
    } else if (response.requiresLogout) {
      await logout();
    }
    return false;
  }

  // ─── Mise à jour du profil ───
  Future<bool> updateProfile({String? name, String? email}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final response = await _api.put(ApiConfig.updateProfile, data: {
      if (name != null) 'name': name,
      if (email != null) 'email': email,
    });

    if (response.success && response.data != null) {
      final userJson = response.data as Map<String, dynamic>?;
      if (userJson != null) {
        _currentUser = User.fromJson(userJson);
        await _storage.saveUserData(jsonEncode(userJson));
        _isLoading = false;
        notifyListeners();
        return true;
      }
    }

    _errorMessage = response.message;
    _isLoading = false;
    notifyListeners();
    return false;
  }

  // ─── Effacer le message d'erreur ───
  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}
