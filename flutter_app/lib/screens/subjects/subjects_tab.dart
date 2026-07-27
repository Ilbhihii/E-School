import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

import '../../config/theme.dart';
import '../../models/subject.dart';
import '../../services/api_client.dart';
import '../../widgets/error_widget.dart';
import 'subject_levels_screen.dart';

class SubjectsTab extends StatefulWidget {
  const SubjectsTab({super.key});

  @override
  State<SubjectsTab> createState() => _SubjectsTabState();
}

class _SubjectsTabState extends State<SubjectsTab> {
  final ApiClient _api = ApiClient();
  List<Subject> _subjects = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadSubjects();
  }

  Future<void> _loadSubjects() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final response = await _api.get('/subjects');
    if (response.success && response.data != null) {
      final list = (response.data as List)
          .map((e) => Subject.fromJson(e as Map<String, dynamic>))
          .toList();
      setState(() {
        _subjects = list;
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
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator(color: AppTheme.gold));
    }

    if (_error != null) {
      return ErrorDisplayWidget(message: _error!, onRetry: _loadSubjects);
    }

    final religious = _subjects.where((s) => s.isReligious).toList();
    final scolaire = _subjects.where((s) => !s.isReligious).toList();

    return RefreshIndicator(
      onRefresh: _loadSubjects,
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text('Matières religieuses',
              style: Theme.of(context).textTheme.titleLarge?.copyWith(fontSize: 15)),
          const SizedBox(height: 8),
          ...religious.map((s) => _SubjectListCard(
                subject: s,
                onTap: () => _openLevels(context, s),
              )),
          const SizedBox(height: 20),
          Text('Matières scolaires',
              style: Theme.of(context).textTheme.titleLarge?.copyWith(fontSize: 15)),
          const SizedBox(height: 8),
          ...scolaire.map((s) => _SubjectListCard(
                subject: s,
                onTap: () => _openLevels(context, s),
              )),
        ],
      ),
    );
  }

  void _openLevels(BuildContext context, Subject subject) {
    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (_) => SubjectLevelsScreen(subject: subject),
      ),
    );
  }
}

class _SubjectListCard extends StatelessWidget {
  final Subject subject;
  final VoidCallback onTap;

  const _SubjectListCard({required this.subject, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: (subject.isReligious ? AppTheme.purple : AppTheme.navyBlue)
                      .withOpacity(0.2),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Icon(
                  subject.isReligious ? Icons.mosque_rounded : Icons.translate_rounded,
                  color: subject.isReligious ? AppTheme.purple : AppTheme.navyBlue,
                  size: 32,
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(subject.name,
                        style: GoogleFonts.poppins(
                          fontWeight: FontWeight.w600,
                          color: Colors.white,
                          fontSize: 17,
                        )),
                    const SizedBox(height: 4),
                    Text(
                      '${subject.levelsCount} niveaux · ${subject.coursesCount} cours · ${subject.classesCount} classes',
                      style: GoogleFonts.inter(
                        color: AppTheme.textSecondary,
                        fontSize: 13,
                      ),
                    ),
                  ],
                ),
              ),
              const Icon(Icons.chevron_right_rounded,
                  color: AppTheme.textSecondary, size: 28),
            ],
          ),
        ),
      ),
    );
  }
}
