class UserProgress {
  final int totalCourses;
  final int completedCourses;
  final double completionPercentage;
  final List<RecentProgress> recentProgress;

  UserProgress({
    this.totalCourses = 0,
    this.completedCourses = 0,
    this.completionPercentage = 0,
    this.recentProgress = const [],
  });

  factory UserProgress.fromJson(Map<String, dynamic> json) {
    return UserProgress(
      totalCourses: json['total_courses'] ?? 0,
      completedCourses: json['completed_courses'] ?? 0,
      completionPercentage: (json['completion_percentage'] ?? 0).toDouble(),
      recentProgress: (json['recent_progress'] as List?)
              ?.map((p) => RecentProgress.fromJson(p))
              .toList() ??
          [],
    );
  }
}

class RecentProgress {
  final int? courseId;
  final String? courseTitle;
  final bool completed;
  final int? score;
  final DateTime? updatedAt;

  RecentProgress({
    this.courseId,
    this.courseTitle,
    this.completed = false,
    this.score,
    this.updatedAt,
  });

  factory RecentProgress.fromJson(Map<String, dynamic> json) {
    return RecentProgress(
      courseId: json['course_id'],
      courseTitle: json['course_title'],
      completed: json['completed'] ?? false,
      score: json['score'],
      updatedAt: json['updated_at'] != null ? DateTime.tryParse(json['updated_at']) : null,
    );
  }
}

class SubjectProgress {
  final int subjectId;
  final String subjectName;
  final int totalCourses;
  final int completedCourses;
  final double completionPercentage;

  SubjectProgress({
    required this.subjectId,
    required this.subjectName,
    this.totalCourses = 0,
    this.completedCourses = 0,
    this.completionPercentage = 0,
  });

  factory SubjectProgress.fromJson(Map<String, dynamic> json) {
    return SubjectProgress(
      subjectId: json['subject_id'] ?? 0,
      subjectName: json['subject_name'] ?? '',
      totalCourses: json['total_courses'] ?? 0,
      completedCourses: json['completed_courses'] ?? 0,
      completionPercentage: (json['completion_percentage'] ?? 0).toDouble(),
    );
  }
}
