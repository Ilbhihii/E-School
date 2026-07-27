import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

import '../../config/theme.dart';
import '../../models/course.dart';
import '../../services/api_client.dart';
import '../../services/auth_service.dart';
import '../../widgets/video_player_widget.dart';
import 'quiz_screen.dart';

class CourseDetailScreen extends StatefulWidget {
  final Course course;

  const CourseDetailScreen({super.key, required this.course});

  @override
  State<CourseDetailScreen> createState() => _CourseDetailScreenState();
}

class _CourseDetailScreenState extends State<CourseDetailScreen> {
  final ApiClient _api = ApiClient();
  Course? _course;
  bool _isLoading = true;
  bool _isMarkingComplete = false;

  @override
  void initState() {
    super.initState();
    _loadCourseDetail();
  }

  Future<void> _loadCourseDetail() async {
    setState(() => _isLoading = true);

    final response = await _api.get('/courses/${widget.course.id}');
    if (response.success && response.data != null) {
      setState(() {
        _course = Course.fromJson(response.data as Map<String, dynamic>);
        _isLoading = false;
      });
    } else {
      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(response.message ?? 'Erreur de chargement'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }

  Future<void> _markComplete() async {
    setState(() => _isMarkingComplete = true);

    final response = await _api.post('/courses/${widget.course.id}/complete', data: {
      'score': 100,
    });

    setState(() => _isMarkingComplete = false);

    if (response.success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text('✅ Cours marqué comme terminé !'),
          backgroundColor: AppTheme.success,
          behavior: SnackBarBehavior.floating,
        ),
      );
      _loadCourseDetail();
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(response.message ?? 'Erreur'),
          backgroundColor: AppTheme.error,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final course = _course ?? widget.course;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Détail du cours'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppTheme.gold))
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // ─── En-tête ───
                  _buildHeader(course),

                  const SizedBox(height: 20),

                  // ─── Vidéo ───
                  if (course.videoUrl != null || course.video != null || course.courseLink != null) ...[
                    VideoPlayerWidget(
                      videoUrl: course.videoUrl,
                      videoPath: course.video,
                      courseLink: course.courseLink,
                    ),
                    const SizedBox(height: 20),
                  ],

                  // ─── Description ───
                  Text('Description',
                      style: Theme.of(context).textTheme.titleLarge),
                  const SizedBox(height: 8),
                  Card(
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Text(
                        course.description ?? 'Aucune description.',
                        style: GoogleFonts.inter(
                          color: AppTheme.textSecondary,
                          fontSize: 14,
                          height: 1.6,
                        ),
                      ),
                    ),
                  ),

                  const SizedBox(height: 20),

                  // ─── Quiz ───
                  if (course.hasTest == true && course.progress?['completed'] != true) ...[
                    _buildQuizCard(course),
                    const SizedBox(height: 20),
                  ],

                  // ─── Ressources ───
                  if (course.pdf != null) ...[
                    _buildResourceCard(
                      icon: Icons.picture_as_pdf_rounded,
                      title: 'Document PDF',
                      subtitle: 'Télécharger le cours en PDF',
                      onTap: () {
                        // TODO: ouvrir le PDF
                      },
                    ),
                    const SizedBox(height: 12),
                  ],

                  // ─── Progression ───
                  if (course.progress != null) ...[
                    const SizedBox(height: 12),
                    _buildProgressCard(course),
                  ],

                  const SizedBox(height: 24),

                  // ─── Bouton Marquer terminé ───
                  SizedBox(
                    width: double.infinity,
                    height: 52,
                    child: ElevatedButton.icon(
                      onPressed: _isMarkingComplete ? null : _markComplete,
                      icon: _isMarkingComplete
                          ? const SizedBox(
                              width: 20,
                              height: 20,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                color: Colors.white,
                              ),
                            )
                          : Icon(
                              course.progress?['completed'] == true
                                  ? Icons.check_circle
                                  : Icons.check_circle_outline,
                            ),
                      label: Text(
                        course.progress?['completed'] == true
                            ? 'Déjà complété ✓'
                            : 'Marquer comme terminé',
                      ),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: course.progress?['completed'] == true
                            ? AppTheme.success
                            : AppTheme.gold,
                        foregroundColor: course.progress?['completed'] == true
                            ? Colors.white
                            : AppTheme.primaryDark,
                        disabledBackgroundColor: AppTheme.gold.withOpacity(0.4),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildHeader(Course course) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: AppTheme.navyBlue.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    course.subject?['name'] ?? '',
                    style: GoogleFonts.inter(
                      color: AppTheme.navyBlue,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                if (course.isFree)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: AppTheme.success.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      'GRATUIT',
                      style: GoogleFonts.inter(
                        color: AppTheme.success,
                        fontSize: 11,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 14),
            Text(course.title,
                style: GoogleFonts.poppins(
                  fontWeight: FontWeight.w700,
                  color: Colors.white,
                  fontSize: 20,
                )),
            if (course.level != null) ...[
              const SizedBox(height: 8),
              Row(
                children: [
                  const Icon(Icons.school_rounded,
                      size: 16, color: AppTheme.textSecondary),
                  const SizedBox(width: 6),
                  Text(course.level!['name'] ?? '',
                      style: GoogleFonts.inter(
                        color: AppTheme.textSecondary,
                        fontSize: 13,
                      )),
                ],
              ),
            ],
          ],
        ),
      ),
    );
  }



  bool _isLoadingQuiz = false;

  Widget _buildQuizCard(Course course) {
    return Card(
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: _isLoadingQuiz
            ? null
            : () async {
                setState(() => _isLoadingQuiz = true);
                final courseDetail = _course ?? widget.course;
                if (courseDetail.test == null) {
                  await _loadCourseDetail();
                }
                if (!mounted) return;
                setState(() => _isLoadingQuiz = false);
                final updated = _course ?? widget.course;
                if (updated.test != null) {
                  final test = CourseTest.fromJson(
                      updated.test as Map<String, dynamic>);
                  final score = await Navigator.of(context).push<double>(
                    MaterialPageRoute(
                      builder: (_) => QuizScreen(
                        test: test,
                        courseId: updated.id,
                      ),
                    ),
                  );
                  if (score != null) {
                    _loadCourseDetail();
                  }
                }
              },
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: AppTheme.purple.withOpacity(0.15),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: _isLoadingQuiz
                    ? const SizedBox(
                        width: 24,
                        height: 24,
                        child: CircularProgressIndicator(
                          strokeWidth: 2.5,
                          color: AppTheme.purple,
                        ),
                      )
                    : const Icon(Icons.quiz_rounded,
                        color: AppTheme.purple, size: 28),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      _isLoadingQuiz
                          ? 'Chargement...'
                          : 'Test de validation',
                      style: GoogleFonts.inter(
                        fontWeight: FontWeight.w600,
                        color: Colors.white,
                        fontSize: 15,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      _isLoadingQuiz
                          ? 'Préparation du quiz'
                          : 'Évaluez vos connaissances',
                      style: GoogleFonts.inter(
                        color: AppTheme.textSecondary,
                        fontSize: 12,
                      ),
                    ),
                  ],
                ),
              ),
              const Icon(Icons.arrow_forward_rounded,
                  color: AppTheme.purple, size: 24),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildResourceCard({
    required IconData icon,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return Card(
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: AppTheme.error.withOpacity(0.15),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(icon, color: AppTheme.error, size: 28),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(title,
                        style: GoogleFonts.inter(
                          fontWeight: FontWeight.w600,
                          color: Colors.white,
                          fontSize: 15,
                        )),
                    const SizedBox(height: 2),
                    Text(subtitle,
                        style: GoogleFonts.inter(
                          color: AppTheme.textSecondary,
                          fontSize: 12,
                        )),
                  ],
                ),
              ),
              const Icon(Icons.download_rounded,
                  color: AppTheme.textSecondary),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildProgressCard(Course course) {
    final completed = course.progress?['completed'] == true;
    final score = course.progress?['score'];

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: (completed ? AppTheme.success : AppTheme.textSecondary)
                    .withOpacity(0.15),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(
                completed ? Icons.check_circle_rounded : Icons.hourglass_empty_rounded,
                color: completed ? AppTheme.success : AppTheme.textSecondary,
                size: 24,
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    completed ? 'Cours terminé' : 'En cours',
                    style: GoogleFonts.inter(
                      fontWeight: FontWeight.w600,
                      color: Colors.white,
                    ),
                  ),
                  if (score != null) ...[
                    const SizedBox(height: 4),
                    Text('Score: $score%',
                        style: GoogleFonts.inter(
                          color: AppTheme.textSecondary,
                          fontSize: 13,
                        )),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
