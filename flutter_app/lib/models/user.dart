class User {
  final int id;
  final String name;
  final String email;
  final String role;
  final String? profilePhoto;
  final bool isActive;
  final bool isPaid;
  final String? subscriptionType;
  final bool testPassed;
  final int? classId;
  final String? className;
  final List<Map<String, dynamic>> classes;
  final DateTime? createdAt;

  User({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    this.profilePhoto,
    this.isActive = false,
    this.isPaid = false,
    this.subscriptionType,
    this.testPassed = false,
    this.classId,
    this.className,
    this.classes = const [],
    this.createdAt,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      role: json['role'] ?? 'student',
      profilePhoto: json['profile_photo'],
      isActive: json['is_active'] ?? false,
      isPaid: json['is_paid'] ?? false,
      subscriptionType: json['subscription_type'],
      testPassed: json['test_passed'] ?? false,
      classId: json['class_id'],
      className: json['class_name'],
      classes: (json['classes'] as List?)?.cast<Map<String, dynamic>>() ?? [],
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at']) : null,
    );
  }

  Map<String, dynamic> toJson() => {
    'id': id,
    'name': name,
    'email': email,
    'role': role,
    'profile_photo': profilePhoto,
    'is_active': isActive,
    'is_paid': isPaid,
    'subscription_type': subscriptionType,
    'test_passed': testPassed,
    'class_id': classId,
    'class_name': className,
    'classes': classes,
  };

  bool get isAdmin => role == 'admin';
  bool get isProf  => role == 'prof';
  bool get isStudent => role == 'student';
}
