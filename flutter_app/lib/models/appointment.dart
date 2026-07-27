class Appointment {
  final int id;
  final String firstName;
  final String lastName;
  final String phone;
  final String email;
  final String city;
  final String country;
  final String type;
  final String typeLabel;
  final String status;
  final Map<String, dynamic>? vocalSubmission;
  final DateTime? createdAt;

  Appointment({
    required this.id,
    required this.firstName,
    required this.lastName,
    required this.phone,
    required this.email,
    required this.city,
    required this.country,
    required this.type,
    required this.typeLabel,
    required this.status,
    this.vocalSubmission,
    this.createdAt,
  });

  factory Appointment.fromJson(Map<String, dynamic> json) {
    return Appointment(
      id: json['id'] ?? 0,
      firstName: json['first_name'] ?? '',
      lastName: json['last_name'] ?? '',
      phone: json['phone'] ?? '',
      email: json['email'] ?? '',
      city: json['city'] ?? '',
      country: json['country'] ?? '',
      type: json['type'] ?? '',
      typeLabel: json['type_label'] ?? '',
      status: json['status'] ?? 'pending',
      vocalSubmission: json['vocal_submission'],
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at']) : null,
    );
  }

  bool get isPending => status == 'pending';
  bool get isConfirmed => status == 'confirmed';
}

class AppointmentType {
  final String value;
  final String label;

  AppointmentType({required this.value, required this.label});

  factory AppointmentType.fromJson(Map<String, dynamic> json) {
    return AppointmentType(
      value: json['value'] ?? '',
      label: json['label'] ?? '',
    );
  }
}
