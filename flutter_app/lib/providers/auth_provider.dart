import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../services/api_client.dart';
import '../services/auth_service.dart';

class AuthProvider extends StatelessWidget {
  final Widget child;

  const AuthProvider({super.key, required this.child});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthService()..init()),
        Provider(create: (_) => ApiClient()),
      ],
      child: child,
    );
  }
}
