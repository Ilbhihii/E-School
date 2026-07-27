class Subject {
  final int id;
  final String name;
  final String type; // 'religieux' ou 'scolaire'
  final String? description;
  final String? image;
  final int coursesCount;
  final int levelsCount;
  final int classesCount;

  Subject({
    required this.id,
    required this.name,
    required this.type,
    this.description,
    this.image,
    this.coursesCount = 0,
    this.levelsCount = 0,
    this.classesCount = 0,
  });

  factory Subject.fromJson(Map<String, dynamic> json) {
    return Subject(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      type: json['type'] ?? 'scolaire',
      description: json['description'],
      image: json['image'],
      coursesCount: json['courses_count'] ?? 0,
      levelsCount: json['levels_count'] ?? 0,
      classesCount: json['classes_count'] ?? 0,
    );
  }

  bool get isReligious => type == 'religieux';
}
