class Course {
  final int id;
  final String title;
  final String? description;
  final String? videoUrl;
  final String? video;
  final String? pdf;
  final String? courseLink;
  final bool isFree;
  final int order;
  final Map<String, dynamic>? subject;
  final Map<String, dynamic>? level;
  final Map<String, dynamic>? classRoom;
  final bool? hasTest;
  final Map<String, dynamic>? test;
  final Map<String, dynamic>? progress;
  final DateTime? createdAt;

  Course({
    required this.id,
    required this.title,
    this.description,
    this.videoUrl,
    this.video,
    this.pdf,
    this.courseLink,
    this.isFree = false,
    this.order = 0,
    this.subject,
    this.level,
    this.classRoom,
    this.hasTest,
    this.test,
    this.progress,
    this.createdAt,
  });

  factory Course.fromJson(Map<String, dynamic> json) {
    return Course(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      description: json['description'],
      videoUrl: json['video_url'],
      video: json['video'],
      pdf: json['pdf'],
      courseLink: json['course_link'],
      isFree: json['is_free'] ?? false,
      order: json['order'] ?? 0,
      subject: json['subject'],
      level: json['level'],
      classRoom: json['class'],
      hasTest: json['has_test'],
      test: json['test'],
      progress: json['progress'],
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at']) : null,
    );
  }
}

class CourseTest {
  final int id;
  final String? title;
  final List<Question> questions;

  CourseTest({required this.id, this.title, this.questions = const []});

  factory CourseTest.fromJson(Map<String, dynamic> json) {
    return CourseTest(
      id: json['id'] ?? 0,
      title: json['title'],
      questions: (json['questions'] as List?)
              ?.map((q) => Question.fromJson(q))
              .toList() ??
          [],
    );
  }
}

class Question {
  final int id;
  final String question;
  final String? type;
  final List<Answer> answers;

  Question({required this.id, required this.question, this.type, this.answers = const []});

  factory Question.fromJson(Map<String, dynamic> json) {
    return Question(
      id: json['id'] ?? 0,
      question: json['question'] ?? '',
      type: json['type'],
      answers: (json['answers'] as List?)
              ?.map((a) => Answer.fromJson(a))
              .toList() ??
          [],
    );
  }
}

class Answer {
  final int id;
  final String answer;

  Answer({required this.id, required this.answer});

  factory Answer.fromJson(Map<String, dynamic> json) {
    return Answer(
      id: json['id'] ?? 0,
      answer: json['answer'] ?? '',
    );
  }
}
