class VocalTestSubmission {
  final int id;
  final String? subject;
  final String? level;
  final String? classRoom;
  final String? testTitle;
  final String? testMode;
  final String? status;
  final int? score;
  final DateTime? consumedAt;
  final bool hasAppointment;
  final String? appointmentStatus;
  final DateTime? createdAt;
  final DateTime? submittedAt;

  VocalTestSubmission({
    required this.id,
    this.subject,
    this.level,
    this.classRoom,
    this.testTitle,
    this.testMode,
    this.status,
    this.score,
    this.consumedAt,
    this.hasAppointment = false,
    this.appointmentStatus,
    this.createdAt,
    this.submittedAt,
  });

  factory VocalTestSubmission.fromJson(Map<String, dynamic> json) {
    return VocalTestSubmission(
      id: json['id'] ?? 0,
      subject: json['subject'],
      level: json['level'],
      classRoom: json['class'],
      testTitle: json['test_title'],
      testMode: json['test_mode'],
      status: json['status'],
      score: json['score'],
      consumedAt: json['consumed_at'] != null
          ? DateTime.tryParse(json['consumed_at'])
          : null,
      hasAppointment: json['has_appointment'] ?? false,
      appointmentStatus: json['appointment_status'],
      createdAt: json['created_at'] != null
          ? DateTime.tryParse(json['created_at'])
          : null,
      submittedAt: json['submitted_at'] != null
          ? DateTime.tryParse(json['submitted_at'])
          : null,
    );
  }

  bool get isConsumed => consumedAt != null;

  bool get isUnderReview => status == 'under_review';
  bool get isReviewed => status == 'reviewed' || status == 'accepted';
  bool get needsImprovement => status == 'needs_improvement';

  String get statusLabel {
    switch (status) {
      case 'submitted':
        return 'Soumis';
      case 'under_review':
        return 'En cours d\'évaluation';
      case 'reviewed':
        return 'Évalué';
      case 'accepted':
        return 'Accepté';
      case 'needs_improvement':
        return 'À améliorer';
      default:
        return status ?? 'Soumis';
    }
  }

  String get modeLabel {
    switch (testMode) {
      case 'tajwid':
        return 'Tajwid';
      case 'hifd':
        return 'Hifd (Mémorisation)';
      default:
        return 'Lecture';
    }
  }
}
