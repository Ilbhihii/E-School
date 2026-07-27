class ApiConfig {
  // Changer cette URL selon l'environnement
  static const String baseUrl = 'http://10.0.2.2:8000/api';
  // Pour iOS simulator: http://localhost:8000/api
  // Pour appareil physique: http://<IP_LOCALE>:8000/api

  // ─── Authentification ───
  static const String register        = '/register';
  static const String login           = '/login';
  static const String logout          = '/logout';
  static const String profile         = '/profile';
  static const String updateProfile   = '/profile';
  static const String forgotPassword  = '/forgot-password';

  // ─── Matières ───
  static const String subjects        = '/subjects';
  static String subjectById(int id)   => '/subjects/$id';
  static String subjectLevels(int id) => '/subjects/$id/levels';

  // ─── Niveaux ───
  static String levelById(int id)       => '/levels/$id';
  static String levelClasses(int id)    => '/levels/$id/classes';

  // ─── Classes ───
  static String classById(int id)       => '/classes/$id';
  static String classCourses(int id)    => '/classes/$id/courses';
  static String classSubjects(int id)   => '/classes/$id/subjects';

  // ─── Cours ───
  static const String courses           = '/courses';
  static String courseById(int id)      => '/courses/$id';
  static String courseComplete(int id)  => '/courses/$id/complete';

  // ─── Lives ───
  static const String lives             = '/lives';
  static const String livesUpcoming     = '/lives/upcoming';
  static const String userLives         = '/user/lives';

  // ─── Dashboard ───
  static const String dashboard         = '/dashboard';
  static const String homeStats         = '/home/stats';

  // ─── Rendez-vous ───
  static const String appointments      = '/appointments';
  static const String appointmentTypes  = '/appointments/types';

  // ─── Test vocal ───
  static const String vocalTestText     = '/vocal-test/text';
  static const String vocalTestSubmit   = '/vocal-test/submit';
  static const String vocalTestSubmissions = '/vocal-test/submissions';

  // ─── Progression ───
  static const String progress          = '/progress';
  static const String progressBySubject = '/progress/by-subject';
  static String progressCourse(int id)  => '/progress/$id';
}
