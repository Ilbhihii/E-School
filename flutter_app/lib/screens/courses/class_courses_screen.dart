import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

import '../../config/theme.dart';
import '../../models/course.dart';
import '../../models/class_room.dart';
import '../../models/subject.dart';
import '../../models/level.dart';
import '../../services/api_client.dart';
import '../../widgets/error_widget.dart';
import 'course_detail_screen.dart';

class ClassCoursesScreen extends StatefulWidget {
  final ClassRoom classRoom;
  final Subject subject;
  final Level level;

  const ClassCoursesScreen({
    super.key,
    required this.classRoom,
    required this.subject,
    required this.level,
  });

  @override
  State<ClassCoursesScreen> createState() => _ClassCoursesScreenState();
}

class _ClassCoursesScreenState extends State<ClassCoursesScreen> {
  final ApiClient _api = ApiClient();
  List<Course> _courses = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadCourses();
  }

  Future<void> _loadCourses() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final response = await _api.get(
      '/classes/${widget.classRoom.id}/courses',
      queryParameters: {
        'subject_id': widget.subject.id.toString(),
        'level_id': widget.level.id.toString(),
      },
    );

    if (response.success && response.data != null) {
      final list = (response.data as List)
          .map((e) => Course.fromJson(e as Map<String, dynamic>))
          .toList();
      setState(() {
        _courses = list;
        _isLoading = false;
      });
    } else {
      setState(() {
        _error = response.message;
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('${widget.classRoom.name}'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator(color: AppTheme.gold));
    }

    if (_error != null) {
      return ErrorDisplayWidget(message: _error!, onRetry: _loadCourses);
    }

    if (_courses.isEmpty) {
      return const EmptyStateWidget(message: 'Aucun cours disponible pour cette classe.');
    }

    return RefreshIndicator(
      onRefresh: _loadCourses,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: _courses.length,
        itemBuilder: (context, index) {
          final course = _courses[index];
          final isCompleted = course.progress?['completed'] == true;
          final score = course.progress?['score'];

          return Card(
            margin: const EdgeInsets.only(bottom: 12),
            child: InkWell(
              borderRadius: BorderRadius.circular(16),
              onTap: () {
                Navigator.of(context).push(
                  MaterialPageRoute(
                    builder: (_) => CourseDetailScreen(course: course),
                  ),
                );
              },
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Row(
                  children: [
                    Stack(
                      children: [
                        Container(
                          width: 52,
                          height: 52,
                          decoration: BoxDecoration(
                            gradient: isCompleted
                                ? const LinearGradient(
                                    colors: [AppTheme.success, Color(0xFF16A34A)])
                                : AppTheme.primaryGradient,
                            borderRadius: BorderRadius.circular(14),
                          ),
                          child: Center(
                            child: course.isFree
                                ? const Icon(Icons.lock_open_rounded,
                                    color: Colors.white, size: 24)
                                : const Icon(Icons.menu_book_rounded,
                                    color: Colors.white, size: 24),
                          ),
                        ),
                        if (isCompleted)
                          Positioned(
                            top: -2,
                            right: -2,
                            child: Container(
                              padding: const EdgeInsets.all(2),
                              decoration: const BoxDecoration(
                                color: AppTheme.success,
                                shape: BoxShape.circle,
                              ),
                              child: const Icon(Icons.check,
                                  size: 12, color: Colors.white),
                            ),
                          ),
                      ],
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(course.title,
                              style: GoogleFonts.inter(
                                fontWeight: FontWeight.w600,
                                color: Colors.white,
                                fontSize: 15,
                              ),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis),
                          const SizedBox(height: 6),
                          Row(
                            children: [
                              if (course.isFree)
                                Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 8, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: AppTheme.success.withOpacity(0.15),
                                    borderRadius: BorderRadius.circular(6),
                                  ),
                                  child: Text('GRATUIT',
                                      style: GoogleFonts.inter(
                                        color: AppTheme.success,
                                        fontSize: 10,
                                        fontWeight: FontWeight.w700,
                                        letterSpacing: 0.5,
                                      )),
                                ),
                              if (course.isFree && score != null)
                                const SizedBox(width: 8),
                              if (score != null)
                                Text('Score: $score%',
                                    style: GoogleFonts.inter(
                                      color: AppTheme.textSecondary,
                                      fontSize: 12,
                                    )),
                              if (course.hasTest == true && score == null) ...[
                                if (course.isFree) const SizedBox(width: 8),
                                Text('Avec test',
                                    style: GoogleFonts.inter(
                                      color: AppTheme.gold,
                                      fontSize: 12,
                                    )),
                              ],
                            ],
                          ),
                        ],
                      ),
                    ),
                    const Icon(Icons.play_circle_rounded,
                        color: AppTheme.gold, size: 32),
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}
