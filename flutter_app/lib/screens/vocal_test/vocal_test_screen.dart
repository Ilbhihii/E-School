import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';

import '../../config/theme.dart';
import '../../models/vocal_test.dart';
import '../../services/api_client.dart';
import '../../widgets/error_widget.dart';
import '../../widgets/loading_widget.dart';

class VocalTestScreen extends StatefulWidget {
  const VocalTestScreen({super.key});

  @override
  State<VocalTestScreen> createState() => _VocalTestScreenState();
}

class _VocalTestScreenState extends State<VocalTestScreen>
    with SingleTickerProviderStateMixin {
  final ApiClient _api = ApiClient();
  late TabController _tabController;

  // Texte de récitation
  String? _recitationText;
  String? _recitationSource;
  bool _isLoadingText = true;
  String? _textError;

  // Historique
  List<VocalTestSubmission> _submissions = [];
  bool _isLoadingSubmissions = false;
  String? _submissionsError;

  // Soumission
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _loadText();
    _loadSubmissions();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadText() async {
    setState(() {
      _isLoadingText = true;
      _textError = null;
    });

    final response = await _api.get('/vocal-test/text');
    if (response.success) {
      final data = response.data is Map
          ? (response.data as Map)['data']
          : response.data;
      if (data != null) {
        setState(() {
          _recitationText = data is Map
              ? (data['text'] ?? data['content'] ?? '')
              : data.toString();
          _recitationSource = data is Map ? data['source'] : null;
          _isLoadingText = false;
        });
      } else {
        setState(() {
          _recitationText = '';
          _isLoadingText = false;
        });
      }
    } else {
      setState(() {
        _textError = response.message;
        _isLoadingText = false;
      });
    }
  }

  Future<void> _loadSubmissions() async {
    setState(() {
      _isLoadingSubmissions = true;
      _submissionsError = null;
    });

    final response = await _api.get('/vocal-test/submissions');
    if (response.success) {
      final list = (response.data is Map
              ? (response.data as Map)['data']
              : response.data) as List?;
      setState(() {
        _submissions = (list ?? [])
            .map((e) => VocalTestSubmission.fromJson(e as Map<String, dynamic>))
            .toList();
        _isLoadingSubmissions = false;
      });
    } else {
      setState(() {
        _submissionsError = response.message;
        _isLoadingSubmissions = false;
      });
    }
  }

  Future<void> _submitTest() async {
    // Simuler un enregistrement/test vocal
    // Dans une vraie app, ceci lancerait l'enregistrement audio
    setState(() => _isSubmitting = true);

    final response = await _api.post('/vocal-test/submit', data: {
      // Note: dans une implémentation réelle, ce serait un fichier audio uploadé
      // via l'API uploadFile() après enregistrement
      'recorded_text': _recitationText ?? '',
      'duration_seconds': 0,
    });

    setState(() => _isSubmitting = false);

    if (response.success) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Test vocal soumis avec succès !'),
          backgroundColor: AppTheme.success,
        ),
      );
      _loadSubmissions();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content:
              Text(response.message ?? 'Erreur lors de la soumission.'),
          backgroundColor: AppTheme.error,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.mic_rounded, color: AppTheme.purple, size: 22),
            const SizedBox(width: 8),
            const Text('Test vocal',
                style: TextStyle(fontWeight: FontWeight.w600)),
          ],
        ),
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: AppTheme.gold,
          labelColor: AppTheme.gold,
          unselectedLabelColor: AppTheme.textSecondary,
          tabs: const [
            Tab(text: 'Récitation'),
            Tab(text: 'Soumettre'),
            Tab(text: 'Historique'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildRecitationTab(),
          _buildSubmitTab(),
          _buildHistoryTab(),
        ],
      ),
    );
  }

  // ─── TAB 1 : Texte de récitation ───

  Widget _buildRecitationTab() {
    if (_isLoadingText) {
      return const Center(
          child: CircularProgressIndicator(color: AppTheme.gold));
    }
    if (_textError != null) {
      return ErrorDisplayWidget(
          message: _textError!, onRetry: _loadText);
    }
    if (_recitationText == null || _recitationText!.isEmpty) {
      return const EmptyStateWidget(
        message: 'Aucun texte de récitation disponible.',
      );
    }

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header
          Card(
            child: Container(
              width: double.infinity,
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(16),
                gradient: const LinearGradient(
                  colors: [AppTheme.purple, Color(0xFF5B21B6)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.auto_stories_rounded,
                        color: Colors.white, size: 28),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Texte de récitation',
                          style: GoogleFonts.inter(
                            color: Colors.white.withOpacity(0.8),
                            fontSize: 12,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          _recitationSource ?? 'Récitation',
                          style: GoogleFonts.poppins(
                            color: Colors.white,
                            fontWeight: FontWeight.w700,
                            fontSize: 16,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 20),

          // Texte
          Card(
            child: Container(
              width: double.infinity,
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(Icons.format_quote_rounded,
                          color: AppTheme.gold.withOpacity(0.4), size: 28),
                      const Spacer(),
                      Text(
                        'À réciter à voix haute',
                        style: GoogleFonts.inter(
                          color: AppTheme.textSecondary,
                          fontSize: 12,
                          fontStyle: FontStyle.italic,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    _recitationText!,
                    style: GoogleFonts.amiri(
                      fontSize: 24,
                      height: 1.8,
                      color: Colors.white,
                      fontWeight: FontWeight.w400,
                    ),
                    textDirection: TextDirection.rtl,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 16),
                  Row(
                    children: [
                      const Spacer(),
                      Icon(Icons.format_quote_rounded,
                          color: AppTheme.gold.withOpacity(0.4), size: 28),
                    ],
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 20),

          // Info
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: AppTheme.navyBlue.withOpacity(0.1),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(
                  color: AppTheme.navyBlue.withOpacity(0.2)),
            ),
            child: Row(
              children: [
                Icon(Icons.info_outline_rounded,
                    color: AppTheme.navyBlue, size: 20),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    'Récitez ce texte à voix haute, puis enregistrez-vous dans l\'onglet "Soumettre".',
                    style: GoogleFonts.inter(
                      color: AppTheme.textSecondary,
                      fontSize: 13,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // ─── TAB 2 : Soumettre le test ───

  Widget _buildSubmitTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SizedBox(height: 8),

          // Instruction card
          Card(
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  Container(
                    width: 80,
                    height: 80,
                    decoration: BoxDecoration(
                      color: AppTheme.purple.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Icon(
                      Icons.mic_rounded,
                      size: 40,
                      color: AppTheme.purple,
                    ),
                  ),
                  const SizedBox(height: 20),
                  Text(
                    'Prêt à réciter ?',
                    style: GoogleFonts.poppins(
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Appuyez sur le bouton ci-dessous pour soumettre votre récitation.\nAssurez-vous d\'avoir bien lu le texte dans l\'onglet "Récitation".',
                    textAlign: TextAlign.center,
                    style: GoogleFonts.inter(
                      color: AppTheme.textSecondary,
                      fontSize: 13,
                      height: 1.5,
                    ),
                  ),
                  const SizedBox(height: 24),

                  // Submit button
                  SizedBox(
                    width: double.infinity,
                    height: 52,
                    child: ElevatedButton.icon(
                      onPressed: _isSubmitting ? null : _submitTest,
                      icon: _isSubmitting
                          ? const SizedBox(
                              width: 20,
                              height: 20,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                color: Colors.white,
                              ),
                            )
                          : const Icon(Icons.send_rounded),
                      label: Text(
                        _isSubmitting
                            ? 'Envoi en cours…'
                            : 'Soumettre ma récitation',
                        style: GoogleFonts.inter(
                            fontWeight: FontWeight.w700),
                      ),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppTheme.purple,
                        foregroundColor: Colors.white,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),

          const SizedBox(height: 20),

          // Tips
          Text('Conseils',
              style: GoogleFonts.inter(
                  fontWeight: FontWeight.w600,
                  color: Colors.white,
                  fontSize: 15)),
          const SizedBox(height: 12),
          _TipRow(
            icon: Icons.volume_up_rounded,
            text: 'Récitez à voix haute et clairement',
          ),
          const SizedBox(height: 8),
          _TipRow(
            icon: Icons.speed_rounded,
            text: 'Prenez votre temps, ne vous précipitez pas',
          ),
          const SizedBox(height: 8),
          _TipRow(
            icon: Icons.hearing_rounded,
            text: 'Assurez-vous d\'être dans un endroit calme',
          ),
        ],
      ),
    );
  }

  // ─── TAB 3 : Historique ───

  Widget _buildHistoryTab() {
    if (_isLoadingSubmissions) {
      return const Center(
          child: CircularProgressIndicator(color: AppTheme.gold));
    }
    if (_submissionsError != null) {
      return ErrorDisplayWidget(
          message: _submissionsError!, onRetry: _loadSubmissions);
    }
    if (_submissions.isEmpty) {
      return const EmptyStateWidget(
        message: 'Aucune soumission pour le moment.',
        actionLabel: 'Faire un test',
      );
    }

    return RefreshIndicator(
      onRefresh: _loadSubmissions,
      child: ListView.builder(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.all(16),
        itemCount: _submissions.length,
        itemBuilder: (context, index) {
          final sub = _submissions[index];
          return Card(
            margin: const EdgeInsets.only(bottom: 12),
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  // Icon
                  Container(
                    width: 48,
                    height: 48,
                    decoration: BoxDecoration(
                      color: (sub.isConsumed
                              ? AppTheme.success
                              : AppTheme.warning)
                          .withOpacity(0.15),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Icon(
                      sub.isConsumed
                          ? Icons.check_circle_rounded
                          : Icons.hourglass_empty_rounded,
                      color: sub.isConsumed
                          ? AppTheme.success
                          : AppTheme.warning,
                      size: 24,
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Subject info
                        Row(
                          children: [
                            if (sub.subject != null) ...[
                              Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 8, vertical: 2),
                                decoration: BoxDecoration(
                                  color:
                                      AppTheme.navyBlue.withOpacity(0.2),
                                  borderRadius: BorderRadius.circular(6),
                                ),
                                child: Text(
                                  sub.subject!,
                                  style: GoogleFonts.inter(
                                    fontSize: 10,
                                    fontWeight: FontWeight.w600,
                                    color: AppTheme.navyBlue,
                                  ),
                                ),
                              ),
                              const SizedBox(width: 6),
                            ],
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 8, vertical: 2),
                              decoration: BoxDecoration(
                                color: (sub.isConsumed
                                        ? AppTheme.success
                                        : AppTheme.warning)
                                    .withOpacity(0.15),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                sub.isConsumed ? 'Traité' : 'En attente',
                                style: GoogleFonts.inter(
                                  fontSize: 10,
                                  fontWeight: FontWeight.w600,
                                  color: sub.isConsumed
                                      ? AppTheme.success
                                      : AppTheme.warning,
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 6),
                        if (sub.level != null || sub.classRoom != null)
                          Text(
                            [
                              if (sub.level != null) sub.level!,
                              if (sub.classRoom != null) sub.classRoom!,
                            ].join(' — '),
                            style: GoogleFonts.inter(
                              color: AppTheme.textSecondary,
                              fontSize: 12,
                            ),
                          ),
                        if (sub.createdAt != null) ...[
                          const SizedBox(height: 4),
                          Text(
                            'Soumis le ${DateFormat('dd MMM yyyy', 'fr_FR').format(sub.createdAt!)}',
                            style: GoogleFonts.inter(
                              color: AppTheme.textSecondary,
                              fontSize: 11,
                            ),
                          ),
                        ],
                        // Appointment link
                        if (sub.hasAppointment) ...[
                          const SizedBox(height: 6),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 8, vertical: 3),
                            decoration: BoxDecoration(
                              color: AppTheme.gold.withOpacity(0.12),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(
                              sub.appointmentStatus == 'confirmed'
                                  ? 'Rendez-vous confirmé ✓'
                                  : 'Rendez-vous lié',
                              style: GoogleFonts.inter(
                                fontSize: 10,
                                fontWeight: FontWeight.w600,
                                color: AppTheme.gold,
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),
                  // Arrow
                  if (sub.hasAppointment)
                    Icon(Icons.chevron_right_rounded,
                        color: AppTheme.textSecondary.withOpacity(0.4)),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}

// ═══════════════════════════════════════════════════════
// TIP ROW
// ═══════════════════════════════════════════════════════

class _TipRow extends StatelessWidget {
  final IconData icon;
  final String text;

  const _TipRow({required this.icon, required this.text});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(6),
          decoration: BoxDecoration(
            color: AppTheme.gold.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, size: 16, color: AppTheme.gold),
        ),
        const SizedBox(width: 12),
        Text(
          text,
          style: GoogleFonts.inter(
            color: AppTheme.textSecondary,
            fontSize: 13,
          ),
        ),
      ],
    );
  }
}
