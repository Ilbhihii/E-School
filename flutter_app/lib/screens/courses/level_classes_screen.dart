import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

import '../../config/theme.dart';
import '../../models/level.dart';
import '../../models/subject.dart';
import '../../models/class_room.dart';
import '../../services/api_client.dart';
import '../../widgets/error_widget.dart';
import 'class_courses_screen.dart';

class LevelClassesScreen extends StatefulWidget {
  final Level level;
  final Subject subject;

  const LevelClassesScreen({
    super.key,
    required this.level,
    required this.subject,
  });

  @override
  State<LevelClassesScreen> createState() => _LevelClassesScreenState();
}

class _LevelClassesScreenState extends State<LevelClassesScreen> {
  final ApiClient _api = ApiClient();
  List<ClassRoom> _classes = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadClasses();
  }

  Future<void> _loadClasses() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final response = await _api.get('/levels/${widget.level.id}/classes');
    if (response.success && response.data != null) {        final data = response.data as Map<String, dynamic>;
        final list = (data['classes'] as List)
            .map((e) => ClassRoom.fromJson(e as Map<String, dynamic>))
            .toList();
      setState(() {
        _classes = list;
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
        title: Text('${widget.level.name} — ${widget.subject.name}'),
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
      return ErrorDisplayWidget(message: _error!, onRetry: _loadClasses);
    }

    if (_classes.isEmpty) {
      return const EmptyStateWidget(message: 'Aucune classe disponible pour ce niveau.');
    }

    return RefreshIndicator(
      onRefresh: _loadClasses,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: _classes.length,
        itemBuilder: (context, index) {
          final classRoom = _classes[index];
          return Card(
            margin: const EdgeInsets.only(bottom: 12),
            child: InkWell(
              borderRadius: BorderRadius.circular(16),
              onTap: () {
                Navigator.of(context).push(
                  MaterialPageRoute(
                    builder: (_) => ClassCoursesScreen(
                      classRoom: classRoom,
                      subject: widget.subject,
                      level: widget.level,
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
                        color: AppTheme.gold.withOpacity(0.15),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: const Icon(Icons.group_rounded,
                          color: AppTheme.gold, size: 26),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(classRoom.name,
                              style: GoogleFonts.inter(
                                fontWeight: FontWeight.w600,
                                color: Colors.white,
                                fontSize: 16,
                              )),
                          const SizedBox(height: 6),
                          Row(
                            children: [
                              _Badge(
                                icon: Icons.menu_book_rounded,
                                label: '${classRoom.coursesCount} cours',
                              ),
                              const SizedBox(width: 12),
                              _Badge(
                                icon: Icons.book_rounded,
                                label: '${classRoom.subjectsCount} matières',
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
