import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

import '../../config/theme.dart';
import '../../models/subject.dart';
import '../../models/level.dart';
import '../../services/api_client.dart';
import '../../widgets/error_widget.dart';
import '../courses/level_classes_screen.dart';

class SubjectLevelsScreen extends StatefulWidget {
  final Subject subject;

  const SubjectLevelsScreen({super.key, required this.subject});

  @override
  State<SubjectLevelsScreen> createState() => _SubjectLevelsScreenState();
}

class _SubjectLevelsScreenState extends State<SubjectLevelsScreen> {
  final ApiClient _api = ApiClient();
  List<Level> _levels = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadLevels();
  }

  Future<void> _loadLevels() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final response = await _api.get('/subjects/${widget.subject.id}/levels');
    if (response.success && response.data != null) {        final data = response.data as Map<String, dynamic>;
        final levels = (data['levels'] as List)
            .map((e) => Level.fromJson(e as Map<String, dynamic>))
            .toList();
      setState(() {
        _levels = levels;
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
        title: Text(widget.subject.name),
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
      return ErrorDisplayWidget(message: _error!, onRetry: _loadLevels);
    }

    if (_levels.isEmpty) {
      return const EmptyStateWidget(message: 'Aucun niveau disponible pour cette matière.');
    }

    return RefreshIndicator(
      onRefresh: _loadLevels,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: _levels.length,
        itemBuilder: (context, index) {
          final level = _levels[index];
          return Card(
            margin: const EdgeInsets.only(bottom: 12),
            child: InkWell(
              borderRadius: BorderRadius.circular(16),
              onTap: () {
                Navigator.of(context).push(
                  MaterialPageRoute(
                    builder: (_) => LevelClassesScreen(
                      level: level,
                      subject: widget.subject,
                    ),
                  ),
                );
              },
              child: Padding(
                padding: const EdgeInsets.all(18),
                child: Row(
                  children: [
                    Container(
                      width: 48,
                      height: 48,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(14),
                        gradient: AppTheme.primaryGradient,
                      ),
                      child: Center(
                        child: Text(
                          '${level.order}',
                          style: GoogleFonts.poppins(
                            fontSize: 18,
                            fontWeight: FontWeight.w700,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(level.name,
                              style: GoogleFonts.inter(
                                fontWeight: FontWeight.w600,
                                color: Colors.white,
                                fontSize: 16,
                              )),
                          if (level.description != null) ...[
                            const SizedBox(height: 4),
                            Text(level.description!,
                                style: GoogleFonts.inter(
                                  color: AppTheme.textSecondary,
                                  fontSize: 13,
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis),
                          ],
                          const SizedBox(height: 6),
                          Row(
                            children: [
                              _Badge(
                                icon: Icons.menu_book_rounded,
                                label: '${level.coursesCount} cours',
                              ),
                              const SizedBox(width: 12),
                              _Badge(
                                icon: Icons.school_rounded,
                                label: '${level.classesCount} classes',
                              ),
                            ],
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
        },
      ),
    );
  }
}

class _Badge extends StatelessWidget {
  final IconData icon;
  final String label;

  const _Badge({required this.icon, required this.label});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 14, color: AppTheme.textSecondary),
        const SizedBox(width: 4),
        Text(label,
            style: GoogleFonts.inter(
              color: AppTheme.textSecondary,
              fontSize: 12,
            )),
      ],
    );
  }
}
