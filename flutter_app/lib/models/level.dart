class Level {
  final int id;
  final String name;
  final String? description;
  final int order;
  final int coursesCount;
  final int classesCount;
  final Map<String, dynamic>? subject;

  Level({
    required this.id,
    required this.name,
    this.description,
    this.order = 0,
    this.coursesCount = 0,
    this.classesCount = 0,
    this.subject,
  });

  factory Level.fromJson(Map<String, dynamic> json) {
    return Level(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      description: json['description'],
      order: json['order'] ?? 0,
      coursesCount: json['courses_count'] ?? 0,
      classesCount: json['classes_count'] ?? 0,
      subject: json['subject'],
    );
  }
}
