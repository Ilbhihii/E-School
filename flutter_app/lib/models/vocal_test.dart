class VocalTestSubmission {
  final int id;
  final String? subject;
  final String? level;
  final String? classRoom;
  final DateTime? consumedAt;
  final bool hasAppointment;
  final String? appointmentStatus;
  final DateTime? createdAt;

  VocalTestSubmission({
    required this.id,
    this.subject,
    this.level,
    this.classRoom,
    this.consumedAt,
    this.hasAppointment = false,
    this.appointmentStatus,
    this.createdAt,
  });

  factory VocalTestSubmission.fromJson(Map<String, dynamic> json) {
    return VocalTestSubmission(
      id: json['id'] ?? 0,
      subject: json['subject'],
      level: json['level'],
      classRoom: json['class'],
      consumedAt: json['consumed_at'] != null ? DateTime.tryParse(json['consumed_at']) : null,
      hasAppointment: json['has_appointment'] ?? false,
      appointmentStatus: json['appointment_status'],
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at']) : null,
    );
  }

  bool get isConsumed => consumedAt != null;
}
