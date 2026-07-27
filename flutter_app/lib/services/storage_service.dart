import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';

class StorageService {
  static StorageService? _instance;
  late final FlutterSecureStorage _secure;
  late final SharedPreferences _prefs;

  static const String _tokenKey = 'auth_token';
  static const String _userDataKey = 'user_data';
  static const String _rememberEmailKey = 'remember_email';

  StorageService._internal();

  static Future<StorageService> getInstance() async {
    if (_instance == null) {
      _instance = StorageService._internal();
      _instance!._secure = const FlutterSecureStorage(
        aOptions: AndroidOptions(encryptedSharedPreferences: true),
      );
      _instance!._prefs = await SharedPreferences.getInstance();
    }
    return _instance!;
  }

  // ─── Token ───
  Future<void> saveToken(String token) async {
    await _secure.write(key: _tokenKey, value: token);
  }

  Future<String?> getToken() async {
    return await _secure.read(key: _tokenKey);
  }

  Future<void> deleteToken() async {
    await _secure.delete(key: _tokenKey);
  }

  // ─── User data (JSON) ───
  Future<void> saveUserData(String json) async {
    await _prefs.setString(_userDataKey, json);
  }

  String? getUserData() {
    return _prefs.getString(_userDataKey);
  }

  Future<void> clearUserData() async {
    await _prefs.remove(_userDataKey);
  }

  // ─── Remember email ───
  Future<void> saveRememberEmail(String email) async {
    await _prefs.setString(_rememberEmailKey, email);
  }

  String? getRememberEmail() {
    return _prefs.getString(_rememberEmailKey);
  }

  Future<void> clearRememberEmail() async {
    await _prefs.remove(_rememberEmailKey);
  }

  // ─── Nettoyage complet ───
  Future<void> clearAll() async {
    await deleteToken();
    await clearUserData();
    await _secure.deleteAll();
  }
}
