import 'subject.dart';

class ClassRoom {
  final int id;
  final String name;
  final Map<String, dynamic>? level;
  final int coursesCount;
  final int subjectsCount;
  final int studentsCount;
  final List<Subject> subjects;

  ClassRoom({
    required this.id,
    required this.name,
    this.level,
    this.coursesCount = 0,
    this.subjectsCount = 0,
    this.studentsCount = 0,
    this.subjects = const [],
  });

  factory ClassRoom.fromJson(Map<String, dynamic> json) {
    return ClassRoom(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      level: json['level'],
      coursesCount: json['courses_count'] ?? 0,
      subjectsCount: json['subjects_count'] ?? 0,
      studentsCount: json['students_count'] ?? 0,
      subjects: (json['subjects'] as List?)
              ?.map((s) => Subject.fromJson(s))
              .toList() ??
          [],
    );
  }
}
