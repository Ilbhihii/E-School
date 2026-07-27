import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';

import '../config/api_config.dart';

class ApiClient {
  static ApiClient? _instance;
  late final Dio _dio;

  // Token stocké en mémoire
  String? _authToken;

  ApiClient._internal() {
    _dio = Dio(BaseOptions(
      baseUrl: ApiConfig.baseUrl,
      connectTimeout: const Duration(seconds: 15),
      receiveTimeout: const Duration(seconds: 15),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ));

    // Intercepteur pour le token d'authentification
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) {
        if (_authToken != null) {
          options.headers['Authorization'] = 'Bearer $_authToken';
        }
        handler.next(options);
      },
      onError: (error, handler) {
        // Gestion centralisée des erreurs
        handler.next(error);
      },
    ));

    // Logger en mode debug
    if (kDebugMode) {
      _dio.interceptors.add(PrettyDioLogger(
        requestHeader: true,
        requestBody: true,
        responseBody: true,
        responseHeader: false,
        compact: true,
      ));
    }
  }

  factory ApiClient() {
    _instance ??= ApiClient._internal();
    return _instance!;
  }

  Dio get dio => _dio;

  // ─── Gestion du token ───

  void setToken(String? token) {
    _authToken = token;
  }

  bool get hasToken => _authToken != null && _authToken!.isNotEmpty;

  void clearToken() {
    _authToken = null;
  }

  // ─── Méthodes HTTP simplifiées ───

  Future<ApiResponse> get(
    String path, {
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await _dio.get(
        path,
        queryParameters: queryParameters,
      );
      return ApiResponse.success(response.data);
    } on DioException catch (e) {
      return _handleError(e);
    }
  }

  Future<ApiResponse> post(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await _dio.post(
        path,
        data: data,
        queryParameters: queryParameters,
      );
      return ApiResponse.success(response.data);
    } on DioException catch (e) {
      return _handleError(e);
    }
  }

  Future<ApiResponse> put(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await _dio.put(
        path,
        data: data,
        queryParameters: queryParameters,
      );
      return ApiResponse.success(response.data);
    } on DioException catch (e) {
      return _handleError(e);
    }
  }

  Future<ApiResponse> delete(String path) async {
    try {
      final response = await _dio.delete(path);
      return ApiResponse.success(response.data);
    } on DioException catch (e) {
      return _handleError(e);
    }
  }

  Future<ApiResponse> uploadFile(
    String path, {
    required String filePath,
    required String fieldName,
    Map<String, dynamic>? extraFields,
  }) async {
    try {
      final formData = FormData.fromMap({
        if (extraFields != null) ...extraFields,
        fieldName: await MultipartFile.fromFile(filePath),
      });
      final response = await _dio.post(path, data: formData);
      return ApiResponse.success(response.data);
    } on DioException catch (e) {
      return _handleError(e);
    }
  }

  // ─── Gestion d'erreurs ───

  ApiResponse _handleError(DioException e) {
    switch (e.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.sendTimeout:
      case DioExceptionType.receiveTimeout:
        return ApiResponse.error(
          'La connexion a expiré. Vérifiez votre réseau.',
          statusCode: 408,
        );
      case DioExceptionType.connectionError:
        return ApiResponse.error(
          'Impossible de se connecter au serveur.',
          statusCode: 503,
        );
      case DioExceptionType.badResponse:
        final statusCode = e.response?.statusCode ?? 500;
        final data = e.response?.data;

        if (statusCode == 401) {
          return ApiResponse.error(
            'Session expirée. Veuillez vous reconnecter.',
            statusCode: 401,
            requiresLogout: true,
          );
        }

        if (statusCode == 422 && data is Map<String, dynamic>) {
          // Erreurs de validation
          final errors = data['errors'] as Map<String, dynamic>?;
          final firstError = errors?.values.firstOrNull;
          final message = firstError is List ? firstError.first : data['message'] ?? 'Données invalides.';
          return ApiResponse.error(
            message.toString(),
            statusCode: 422,
            errors: errors,
          );
        }

        final message = data is Map<String, dynamic>
            ? (data['message'] ?? 'Une erreur est survenue.')
            : 'Une erreur est survenue.';
        return ApiResponse.error(
          message.toString(),
          statusCode: statusCode,
        );
      default:
        return ApiResponse.error(
          'Erreur réseau. Vérifiez votre connexion.',
        );
    }
  }
}

// ─── Classe générique de réponse API ───

class ApiResponse {
  final bool success;
  final dynamic data;
  final String? message;
  final int? statusCode;
  final bool requiresLogout;
  final Map<String, dynamic>? errors;

  ApiResponse({
    required this.success,
    this.data,
    this.message,
    this.statusCode,
    this.requiresLogout = false,
    this.errors,
  });

  factory ApiResponse.success(dynamic responseData) {
    if (responseData is Map<String, dynamic>) {
      return ApiResponse(
        success: responseData['success'] ?? true,
        data: responseData['data'],
        message: responseData['message'],
      );
    }
    return ApiResponse(success: true, data: responseData);
  }

  factory ApiResponse.error(
    String message, {
    int? statusCode,
    bool requiresLogout = false,
    Map<String, dynamic>? errors,
  }) {
    return ApiResponse(
      success: false,
      message: message,
      statusCode: statusCode,
      requiresLogout: requiresLogout,
      errors: errors,
    );
  }
}
