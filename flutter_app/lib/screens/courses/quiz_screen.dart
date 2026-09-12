import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

import '../../config/theme.dart';
import '../../models/course.dart';
import '../../services/api_client.dart';

class QuizScreen extends StatefulWidget {
  final CourseTest test;
  final int courseId;

  const QuizScreen({
    super.key,
    required this.test,
    required this.courseId,
  });

  @override
  State<QuizScreen> createState() => _QuizScreenState();
}

class _QuizScreenState extends State<QuizScreen>
    with SingleTickerProviderStateMixin {
  final ApiClient _api = ApiClient();

  // État du quiz
  int _currentQuestion = 0;
  final Map<int, int> _selectedAnswers = {}; // questionId -> answerId
  bool _isSubmitted = false;
  bool _isSaving = false;
  double _finalScore = 0;

  late AnimationController _animController;
  late Animation<double> _fadeAnimation;

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 400),
    );
    _fadeAnimation = Tween<double>(begin: 0, end: 1).animate(
      CurvedAnimation(parent: _animController, curve: Curves.easeIn),
    );
    _animController.forward();
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  int get _totalQuestions => widget.test.questions.length;
  double get _progress => (_currentQuestion + 1) / _totalQuestions;
  bool get _hasNext => _currentQuestion < _totalQuestions - 1;
  bool get _canSubmit => _selectedAnswers.length == _totalQuestions;

  void _selectAnswer(int questionId, int answerId) {
    if (_isSubmitted) return;
    setState(() {
      _selectedAnswers[questionId] = answerId;
    });
  }

  void _nextQuestion() {
    if (_hasNext) {
      setState(() {
        _currentQuestion++;
      });
      _animController.reset();
      _animController.forward();
    }
  }

  void _prevQuestion() {
    if (_currentQuestion > 0) {
      setState(() {
        _currentQuestion--;
      });
      _animController.reset();
      _animController.forward();
    }
  }

  Future<void> _submitQuiz() async {
    if (!_canSubmit) return;

    setState(() => _isSaving = true);

    // Les réponses correctes restent côté serveur. Cette action marque
    // uniquement la consultation comme terminée ; elle ne fabrique pas de note.
    final response = await _api.post('/courses/${widget.courseId}/complete');

    if (!mounted) return;

    setState(() {
      _isSubmitted = true;
      _finalScore = 0;
      _isSaving = false;
    });

    if (!response.success) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(response.message ?? 'Erreur lors de la sauvegarde'),
          backgroundColor: AppTheme.error,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final question = widget.test.questions[_currentQuestion];

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.test.title ?? 'Quiz'),
        leading: IconButton(
          icon: const Icon(Icons.close_rounded),
          onPressed: () {
            if (_isSubmitted) {
              Navigator.pop(context, _finalScore);
            } else {
              _showExitDialog();
            }
          },
        ),
      ),
      body: _isSubmitted ? _buildResults() : _buildQuiz(question),
    );
  }

  Widget _buildQuiz(Question question) {
    return Column(
      children: [
        // ─── Barre de progression ───
        _buildProgressBar(),

        Expanded(
          child: FadeTransition(
            opacity: _fadeAnimation,
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // ─── Question ───
                  const SizedBox(height: 12),
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: AppTheme.gold.withOpacity(0.12),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      'Question ${_currentQuestion + 1}/$_totalQuestions',
                      style: GoogleFonts.inter(
                        color: AppTheme.gold,
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    question.question,
                    style: GoogleFonts.poppins(
                      color: Colors.white,
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                      height: 1.4,
                    ),
                  ),
                  const SizedBox(height: 28),

                  // ─── Réponses ───
                  ...question.answers.map((answer) {
                    final isSelected =
                        _selectedAnswers[question.id] == answer.id;
                    return Padding(
                      padding: const EdgeInsets.only(bottom: 12),
                      child: _AnswerCard(
                        answer: answer.answer,
                        index: question.answers.indexOf(answer),
                        isSelected: isSelected,
                        onTap: () => _selectAnswer(question.id, answer.id),
                      ),
                    );
                  }),
                ],
              ),
            ),
          ),
        ),

        // ─── Navigation ───
        _buildNavigation(),
      ],
    );
  }

  Widget _buildProgressBar() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
      color: AppTheme.surfaceDark,
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                '${_selectedAnswers.length}/$_totalQuestions répondues',
                style: GoogleFonts.inter(
                  color: AppTheme.textSecondary,
                  fontSize: 13,
                ),
              ),
              Text(
                '${(_progress * 100).round()}%',
                style: GoogleFonts.inter(
                  color: AppTheme.gold,
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          ClipRRect(
            borderRadius: BorderRadius.circular(4),
            child: LinearProgressIndicator(
              value: _progress,
              backgroundColor: AppTheme.cardDark,
              valueColor: const AlwaysStoppedAnimation<Color>(AppTheme.gold),
              minHeight: 5,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildNavigation() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppTheme.surfaceDark,
        border: Border(
          top: BorderSide(color: AppTheme.cardDark.withOpacity(0.5)),
        ),
      ),
      child: Row(
        children: [
          if (_currentQuestion > 0)
            Expanded(
              child: OutlinedButton.icon(
                onPressed: _prevQuestion,
                icon: const Icon(Icons.arrow_back_rounded, size: 18),
                label: const Text('Précédent'),
                style: OutlinedButton.styleFrom(
                  foregroundColor: AppTheme.textSecondary,
                  side: const BorderSide(color: AppTheme.cardDark),
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
              ),
            ),
          if (_currentQuestion > 0) const SizedBox(width: 12),
          Expanded(
            child: _hasNext
                ? ElevatedButton.icon(
                    onPressed: _canSubmitForCurrent ? _nextQuestion : null,
                    icon: const Icon(Icons.arrow_forward_rounded, size: 18),
                    label: const Text('Suivante'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.navyBlue,
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: AppTheme.navyBlue.withOpacity(0.3),
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  )
                : ElevatedButton.icon(
                    onPressed: _canSubmit ? _submitQuiz : null,
                    icon: _isSaving
                        ? const SizedBox(
                            width: 18,
                            height: 18,
                            child: CircularProgressIndicator(
                                strokeWidth: 2, color: Colors.white),
                          )
                        : const Icon(Icons.check_rounded, size: 18),
                    label: Text(_isSaving ? 'Enregistrement...' : 'Terminer'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.success,
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: AppTheme.success.withOpacity(0.3),
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  ),
          ),
        ],
      ),
    );
  }

  bool get _canSubmitForCurrent {
    final q = widget.test.questions[_currentQuestion];
    return _selectedAnswers.containsKey(q.id);
  }

  Widget _buildResults() {
    return Padding(
      padding: const EdgeInsets.all(24),
      child: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.check_circle_rounded, size: 84, color: AppTheme.success),
            const SizedBox(height: 24),
            Text(
              'Réponses enregistrées',
              style: GoogleFonts.poppins(
                fontSize: 22,
                fontWeight: FontWeight.w700,
                color: Colors.white,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Cette étape marque le cours comme consulté. Aucune note n’est calculée sur l’appareil.',
              textAlign: TextAlign.center,
              style: GoogleFonts.inter(color: AppTheme.textSecondary, fontSize: 14),
            ),
            const SizedBox(height: 32),
            SizedBox(
              width: double.infinity,
              height: 52,
              child: ElevatedButton.icon(
                onPressed: () => Navigator.pop(context),
                icon: const Icon(Icons.check_rounded),
                label: const Text('Terminer'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.gold,
                  foregroundColor: AppTheme.primaryDark,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showExitDialog() {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: AppTheme.surfaceDark,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
        title: const Text('Quitter le quiz ?',
            style: TextStyle(color: Colors.white)),
        content: const Text(
          'Votre progression ne sera pas sauvegardée.',
          style: TextStyle(color: AppTheme.textSecondary),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Continuer',
                style: TextStyle(color: AppTheme.gold)),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(ctx);
              Navigator.pop(context);
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.error,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
            ),
            child: const Text('Quitter'),
          ),
        ],
      ),
    );
  }
}

// ═══════════════════════════════════════════════════════
// WIDGETS
// ═══════════════════════════════════════════════════════

class _AnswerCard extends StatelessWidget {
  final String answer;
  final int index;
  final bool isSelected;
  final VoidCallback onTap;

  const _AnswerCard({
    required this.answer,
    required this.index,
    required this.isSelected,
    required this.onTap,
  });

  static const _letters = ['A', 'B', 'C', 'D', 'E', 'F'];

  @override
  Widget build(BuildContext context) {
    final letter = index < _letters.length ? _letters[index] : '${index + 1}';

    return AnimatedContainer(
      duration: const Duration(milliseconds: 200),
      curve: Curves.easeInOut,
      child: Card(
        color: isSelected ? AppTheme.navyBlue.withOpacity(0.2) : null,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(14),
          side: isSelected
              ? const BorderSide(color: AppTheme.navyBlue, width: 2)
              : BorderSide.none,
        ),
        child: InkWell(
          borderRadius: BorderRadius.circular(14),
          onTap: onTap,
          child: Padding(
            padding: const EdgeInsets.all(14),
            child: Row(
              children: [
                Container(
                  width: 36,
                  height: 36,
                  decoration: BoxDecoration(
                    color: isSelected
                        ? AppTheme.navyBlue
                        : AppTheme.cardDark,
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Center(
                    child: Text(
                      letter,
                      style: GoogleFonts.poppins(
                        color: isSelected ? Colors.white : AppTheme.textSecondary,
                        fontWeight: FontWeight.w700,
                        fontSize: 14,
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Text(
                    answer,
                    style: GoogleFonts.inter(
                      color: isSelected ? Colors.white : AppTheme.textSecondary,
                      fontSize: 14,
                      fontWeight: isSelected ? FontWeight.w600 : FontWeight.w400,
                      height: 1.3,
                    ),
                  ),
                ),
                if (isSelected)
                  const Icon(Icons.check_circle_rounded,
                      color: AppTheme.navyBlue, size: 22),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
