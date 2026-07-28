import 'dart:async';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:record/record.dart';
import 'package:path_provider/path_provider.dart';

import '../../config/theme.dart';
import '../../models/vocal_test.dart';
import '../../services/api_client.dart';
import '../../widgets/error_widget.dart';
import '../../widgets/loading_widget.dart';

class VocalTestScreen extends StatefulWidget {
  /// Identifiants matière / niveau / classe transmis depuis l'écran précédent
  final int? subjectId;
  final int? levelId;
  final int? classId;

  const VocalTestScreen({
    super.key,
    this.subjectId,
    this.levelId,
    this.classId,
  });

  @override
  State<VocalTestScreen> createState() => _VocalTestScreenState();
}

class _VocalTestScreenState extends State<VocalTestScreen>
    with SingleTickerProviderStateMixin {
  final ApiClient _api = ApiClient();
  final AudioRecorder _recorder = AudioRecorder();
  late TabController _tabController;

  // ── Texte de récitation ──
  Map<String, dynamic>? _recitationData;
  bool _isLoadingText = true;
  String? _textError;

  // ── Enregistrement ──
  bool _isRecording = false;
  bool _hasRecording = false;
  String? _audioPath;
  int _recordDuration = 0;
  String? _recordError;
  Timer? _recordTimer;

  // ── Historique ──
  List<VocalTestSubmission> _submissions = [];
  bool _isLoadingSubmissions = false;
  String? _submissionsError;

  // ── Soumission ──
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
    _recordTimer?.cancel();
    _tabController.dispose();
    _recorder.dispose();
    super.dispose();
  }

  // ═══════════════════════════════════════════════════════
  // CHARGEMENT DES DONNÉES
  // ═══════════════════════════════════════════════════════

  Future<void> _loadText() async {
    setState(() {
      _isLoadingText = true;
      _textError = null;
    });

    // Si on a subject_id/level_id/class_id, on les passe en paramètres
    final queryParams = <String, dynamic>{};
    if (widget.subjectId != null) queryParams['subject_id'] = widget.subjectId;
    if (widget.levelId != null) queryParams['level_id'] = widget.levelId;
    if (widget.classId != null) queryParams['class_id'] = widget.classId;

    final response = await _api.get(
      '/vocal-test/text',
      queryParameters: queryParams.isNotEmpty ? queryParams : null,
    );

    if (response.success) {
      final data = response.data;
      if (data is Map<String, dynamic> && data.isNotEmpty) {
        setState(() {
          // ✅ ApiClient extrait déjà `data` → on utilise directement
          _recitationData = data;
          _isLoadingText = false;
        });
      } else {
        setState(() {
          _isLoadingText = false;
          _textError = 'Aucun texte disponible pour cette sélection.';
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
      final list = response.data as List?;
      setState(() {
        // ✅ Utilisation directe de response.data (ApiClient extrait déjà `data`)
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

  // ═══════════════════════════════════════════════════════
  // ENREGISTREMENT AUDIO
  // ═══════════════════════════════════════════════════════

  Future<void> _startRecording() async {
    try {
      // Vérifier et demander la permission
      final hasPermission = await _recorder.hasPermission();
      if (!hasPermission) {
        setState(() => _recordError =
            'Permission microphone refusée. Autorisez-la dans les réglages.');
        return;
      }

      // Déterminer le chemin de sortie
      final dir = await getTemporaryDirectory();
      final path =
          '${dir.path}/enregistrement-${DateTime.now().millisecondsSinceEpoch}.m4a';

      await _recorder.start(
        const RecordConfig(
          encoder: AudioEncoder.aacLc, // format AAC largement supporté
          bitRate: 128000,
          sampleRate: 44100,
        ),
        path: path,
      );

      setState(() {
        _isRecording = true;
        _hasRecording = false;
        _audioPath = null;
        _recordDuration = 0;
        _recordError = null;
      });

      // Timer périodique de suivi de la durée
      _recordTimer = Timer.periodic(
        const Duration(seconds: 1),
        (_) => setState(() => _recordDuration++),
      );
    } catch (e) {
      setState(() {
        _recordError = 'Erreur au démarrage de l\'enregistrement: $e';
      });
    }
  }

  Future<void> _stopRecording() async {
    _recordTimer?.cancel();

    try {
      final path = await _recorder.stop();
      if (path != null && path.isNotEmpty) {
        final file = File(path);
        if (await file.exists()) {
          setState(() {
            _isRecording = false;
            _hasRecording = true;
            _audioPath = path;
          });
          return;
        }
      }
      setState(() {
        _isRecording = false;
        _recordError = 'Fichier audio introuvable.';
      });
    } catch (e) {
      setState(() {
        _isRecording = false;
        _recordError = 'Erreur à l\'arrêt: $e';
      });
    }
  }

  void _resetRecording() {
    setState(() {
      _hasRecording = false;
      _audioPath = null;
      _recordDuration = 0;
      _recordError = null;
    });
  }

  String _formatDuration(int seconds) {
    final m = (seconds ~/ 60).toString().padLeft(2, '0');
    final s = (seconds % 60).toString().padLeft(2, '0');
    return '$m:$s';
  }

  // ═══════════════════════════════════════════════════════
  // SOUMISSION
  // ═══════════════════════════════════════════════════════

  Future<void> _submitTest() async {
    if (_audioPath == null) return;

    setState(() => _isSubmitting = true);

    final extraFields = <String, dynamic>{
      'duration_seconds': _recordDuration,
    };

    // Ajouter les identifiants matière/niveau/classe si disponibles
    if (widget.subjectId != null) extraFields['subject_id'] = widget.subjectId;
    if (widget.levelId != null) extraFields['level_id'] = widget.levelId;
    if (widget.classId != null) extraFields['class_id'] = widget.classId;

    // Si les identifiants viennent du texte chargé (fallback)
    if (_recitationData != null) {
      if (widget.subjectId == null) {
        // Pas encore implémenté - nécessite l'ID matière
      }
    }

    final response = await _api.uploadFile(
      '/vocal-test/submit',
      filePath: _audioPath!,
      fieldName: 'audio',
      extraFields: extraFields.isNotEmpty ? extraFields : null,
    );

    setState(() => _isSubmitting = false);

    if (response.success) {
      _resetRecording();
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Test vocal soumis avec succès !'),
          backgroundColor: AppTheme.success,
        ),
      );
      _loadSubmissions();
      _tabController.animateTo(2); // Va vers l'historique
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

  // ═══════════════════════════════════════════════════════
  // BUILD
  // ═══════════════════════════════════════════════════════

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
            Tab(text: 'Enregistrer'),
            Tab(text: 'Historique'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildRecitationTab(),
          _buildRecordTab(),
          _buildHistoryTab(),
        ],
      ),
    );
  }

  // ═══════════════════════════════════════════════════════
  // TAB 1 : TEXTE DE RÉCITATION
  // ═══════════════════════════════════════════════════════

  Widget _buildRecitationTab() {
    if (_isLoadingText) {
      return const Center(
          child: CircularProgressIndicator(color: AppTheme.gold));
    }
    if (_textError != null) {
      return ErrorDisplayWidget(message: _textError!, onRetry: _loadText);
    }
    if (_recitationData == null || _recitationData!.isEmpty) {
      return const EmptyStateWidget(
        message: 'Aucun texte de récitation disponible.',
      );
    }

    final data = _recitationData!;
    final readingText = data['reading_text'] ?? data['text'] ?? '';
    final title = data['title'] ?? 'Récitation';
    final instructions = data['instructions'] as String?;
    final testMode = data['test_mode'] as String?;
    final maxDuration = data['maximum_duration'] as int? ?? 120;
    final hideText = data['hide_text_during_recording'] as bool? ?? false;
    final subjectName = data['subject'] as String? ?? '';

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
                    child: Icon(
                      testMode == 'hifd'
                          ? Icons.lock_rounded
                          : Icons.auto_stories_rounded,
                      color: Colors.white,
                      size: 28,
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          subjectName.isNotEmpty
                              ? '$subjectName — ${_modeLabel(testMode)}'
                              : 'Texte de récitation',
                          style: GoogleFonts.inter(
                            color: Colors.white.withOpacity(0.8),
                            fontSize: 12,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          title,
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
          const SizedBox(height: 16),

          // Badge durée / mode
          Row(
            children: [
              _InfoBadge(
                icon: testMode == 'hifd'
                    ? Icons.lock_rounded
                    : Icons.access_time_rounded,
                label: 'Max ${maxDuration}s',
              ),
              const SizedBox(width: 8),
              if (testMode != null)
                _InfoBadge(
                  icon: testMode == 'hifd'
                      ? Icons.psychology_rounded
                      : Icons.mic_rounded,
                  label: _modeLabel(testMode),
                ),
            ],
          ),
          const SizedBox(height: 16),

          // Instructions
          if (instructions != null && instructions.isNotEmpty) ...[
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: AppTheme.gold.withOpacity(0.08),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(
                    color: AppTheme.gold.withOpacity(0.15)),
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(Icons.info_outline_rounded,
                      color: AppTheme.gold, size: 20),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      instructions,
                      style: GoogleFonts.inter(
                        color: AppTheme.textSecondary,
                        fontSize: 13,
                        height: 1.5,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
          ],

          // Texte à réciter
          Card(
            child: Container(
              width: double.infinity,
              padding: const EdgeInsets.all(24),
              child: Column(
                children: [
                  Row(
                    children: [
                      Icon(Icons.format_quote_rounded,
                          color: AppTheme.gold.withOpacity(0.4), size: 28),
                      const Spacer(),
                      Text(
                        testMode == 'hifd'
                            ? 'À mémoriser'
                            : 'À réciter à voix haute',
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
                    readingText,
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
          const SizedBox(height: 16),

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
                    testMode == 'hifd'
                        ? 'Mémorisez ce texte, puis enregistrez-vous dans l\'onglet \"Enregistrer\".'
                        : 'Récitez ce texte à voix haute, puis enregistrez-vous dans l\'onglet \"Enregistrer\".',
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

  // ═══════════════════════════════════════════════════════
  // TAB 2 : ENREGISTREMENT AUDIO
  // ═══════════════════════════════════════════════════════

  Widget _buildRecordTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SizedBox(height: 8),

          // Carte d'enregistrement
          Card(
            child: Padding(
              padding: const EdgeInsets.all(24),
              child: Column(
                children: [
                  // Icône / état
                  AnimatedContainer(
                    duration: const Duration(milliseconds: 300),
                    width: 100,
                    height: 100,
                    decoration: BoxDecoration(
                      color: _isRecording
                          ? Colors.red.withOpacity(0.15)
                          : AppTheme.purple.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(30),
                      border: Border.all(
                        color: _isRecording
                            ? Colors.red.withOpacity(0.3)
                            : AppTheme.purple.withOpacity(0.2),
                        width: 2,
                      ),
                    ),
                    child: _isRecording
                        ? _PulsingIcon()
                        : Icon(
                            _hasRecording
                                ? Icons.check_circle_rounded
                                : Icons.mic_rounded,
                            size: 48,
                            color: _hasRecording
                                ? AppTheme.success
                                : AppTheme.purple,
                          ),
                  ),
                  const SizedBox(height: 20),

                  // Timer d'enregistrement
                  if (_isRecording || _hasRecording)
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 16, vertical: 8),
                      decoration: BoxDecoration(
                        color: AppTheme.cardDark,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Text(
                        _formatDuration(_recordDuration),
                        style: GoogleFonts.poppins(
                          fontSize: 36,
                          fontWeight: FontWeight.w700,
                          color: _isRecording
                              ? Colors.red
                              : AppTheme.success,
                          fontFeatures: const [
                            FontFeature.tabularFigures(),
                          ],
                        ),
                      ),
                    ),
                  const SizedBox(height: 16),

                  // Message d'erreur
                  if (_recordError != null)
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(12),
                      margin: const EdgeInsets.only(bottom: 12),
                      decoration: BoxDecoration(
                        color: Colors.red.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Text(
                        _recordError!,
                        style: GoogleFonts.inter(
                          color: Colors.red,
                          fontSize: 13,
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ),

                  // Statut
                  Text(
                    _isRecording
                        ? 'Enregistrement en cours…'
                        : _hasRecording
                            ? 'Enregistrement terminé ✓'
                            : 'Appuyez sur le bouton pour commencer',
                    style: GoogleFonts.inter(
                      color: _isRecording
                          ? Colors.red
                          : _hasRecording
                              ? AppTheme.success
                              : AppTheme.textSecondary,
                      fontSize: 14,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Boutons d'action
                  Row(
                    children: [
                      // STOP
                      if (_isRecording)
                        Expanded(
                          child: SizedBox(
                            height: 52,
                            child: ElevatedButton.icon(
                              onPressed: _stopRecording,
                              icon: const Icon(Icons.stop_rounded),
                              label: const Text('Arrêter'),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.red,
                                foregroundColor: Colors.white,
                              ),
                            ),
                          ),
                        )
                      else ...[
                        // COMMENCER (si pas d'enregistrement)
                        if (!_hasRecording)
                          Expanded(
                            child: SizedBox(
                              height: 52,
                              child: ElevatedButton.icon(
                                onPressed: _startRecording,
                                icon: const Icon(Icons.mic_rounded),
                                label: const Text('Commencer'),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: AppTheme.purple,
                                  foregroundColor: Colors.white,
                                ),
                              ),
                            ),
                          )
                        else ...[
                          // RECOMMENCER
                          Expanded(
                            child: SizedBox(
                              height: 52,
                              child: ElevatedButton.icon(
                                onPressed: _startRecording,
                                icon: const Icon(Icons.refresh_rounded),
                                label: const Text('Réessayer'),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor:
                                      AppTheme.warning.withOpacity(0.2),
                                  foregroundColor: AppTheme.warning,
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(width: 12),
                          // ENVOYER
                          Expanded(
                            child: SizedBox(
                              height: 52,
                              child: ElevatedButton.icon(
                                onPressed: _isSubmitting
                                    ? null
                                    : _submitTest,
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
                                label: Text(_isSubmitting
                                    ? 'Envoi…'
                                    : 'Envoyer'),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: AppTheme.success,
                                  foregroundColor: Colors.white,
                                ),
                              ),
                            ),
                          ),
                        ],
                      ],
                    ],
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 20),

          // Fichier sélectionné
          if (_hasRecording && _audioPath != null)
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: AppTheme.navyBlue.withOpacity(0.08),
                borderRadius: BorderRadius.circular(10),
                border: Border.all(
                    color: AppTheme.navyBlue.withOpacity(0.15)),
              ),
              child: Row(
                children: [
                  Icon(Icons.audio_file_rounded,
                      color: AppTheme.navyBlue, size: 20),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      'Fichier audio prêt (${_formatDuration(_recordDuration)})',
                      style: GoogleFonts.inter(
                        color: AppTheme.textSecondary,
                        fontSize: 12,
                      ),
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.delete_outline_rounded,
                        size: 18, color: AppTheme.error),
                    onPressed: _resetRecording,
                    padding: EdgeInsets.zero,
                    constraints: const BoxConstraints(),
                  ),
                ],
              ),
            ),
          const SizedBox(height: 20),

          // Conseils
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

  // ═══════════════════════════════════════════════════════
  // TAB 3 : HISTORIQUE
  // ═══════════════════════════════════════════════════════

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
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      // Icon
                      Container(
                        width: 48,
                        height: 48,
                        decoration: BoxDecoration(
                          color: _statusColor(sub.status)
                              .withOpacity(0.15),
                          borderRadius: BorderRadius.circular(14),
                        ),
                        child: Icon(
                          _statusIcon(sub.status),
                          color: _statusColor(sub.status),
                          size: 24,
                        ),
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Subject info + status
                            Row(
                              children: [
                                if (sub.subject != null) ...[
                                  Container(
                                    padding: const EdgeInsets.symmetric(
                                        horizontal: 8, vertical: 2),
                                    decoration: BoxDecoration(
                                      color: AppTheme.navyBlue
                                          .withOpacity(0.2),
                                      borderRadius:
                                          BorderRadius.circular(6),
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
                                if (sub.testMode != null)
                                  Container(
                                    padding: const EdgeInsets.symmetric(
                                        horizontal: 8, vertical: 2),
                                    decoration: BoxDecoration(
                                      color: sub.testMode == 'hifd'
                                          ? AppTheme.gold
                                              .withOpacity(0.2)
                                          : AppTheme.purple
                                              .withOpacity(0.2),
                                      borderRadius:
                                          BorderRadius.circular(6),
                                    ),
                                    child: Text(
                                      sub.modeLabel,
                                      style: GoogleFonts.inter(
                                        fontSize: 10,
                                        fontWeight: FontWeight.w600,
                                        color: sub.testMode == 'hifd'
                                            ? AppTheme.gold
                                            : AppTheme.purple,
                                      ),
                                    ),
                                  ),
                                const SizedBox(width: 6),
                                Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 8, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: _statusColor(sub.status)
                                        .withOpacity(0.15),
                                    borderRadius:
                                        BorderRadius.circular(6),
                                  ),
                                  child: Text(
                                    sub.statusLabel,
                                    style: GoogleFonts.inter(
                                      fontSize: 10,
                                      fontWeight: FontWeight.w600,
                                      color: _statusColor(sub.status),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 6),
                            if (sub.testTitle != null)
                              Text(
                                sub.testTitle!,
                                style: GoogleFonts.inter(
                                  color: Colors.white,
                                  fontSize: 14,
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            if (sub.level != null ||
                                sub.classRoom != null)
                              Text(
                                [
                                  if (sub.level != null) sub.level!,
                                  if (sub.classRoom != null)
                                    sub.classRoom!,
                                ].join(' — '),
                                style: GoogleFonts.inter(
                                  color: AppTheme.textSecondary,
                                  fontSize: 12,
                                ),
                              ),
                            if (sub.score != null)
                              Padding(
                                padding: const EdgeInsets.only(top: 4),
                                child: Row(
                                  children: [
                                    Icon(Icons.star_rounded,
                                        size: 16,
                                        color: AppTheme.gold),
                                    const SizedBox(width: 4),
                                    Text(
                                      '${sub.score}/100',
                                      style: GoogleFonts.inter(
                                        fontWeight: FontWeight.w700,
                                        color: AppTheme.gold,
                                        fontSize: 13,
                                      ),
                                    ),
                                  ],
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
                    ],
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  // ═══════════════════════════════════════════════════════
  // AIDES
  // ═══════════════════════════════════════════════════════

  String _modeLabel(String? mode) {
    switch (mode) {
      case 'tajwid':
        return 'Tajwid';
      case 'hifd':
        return 'Mémorisation';
      default:
        return 'Lecture';
    }
  }

  IconData _statusIcon(String? status) {
    switch (status) {
      case 'accepted':
        return Icons.check_circle_rounded;
      case 'needs_improvement':
        return Icons.refresh_rounded;
      case 'reviewed':
        return Icons.rate_review_rounded;
      case 'under_review':
        return Icons.hourglass_top_rounded;
      default:
        return Icons.hourglass_empty_rounded;
    }
  }

  Color _statusColor(String? status) {
    switch (status) {
      case 'accepted':
        return AppTheme.success;
      case 'needs_improvement':
        return AppTheme.warning;
      case 'reviewed':
        return AppTheme.navyBlue;
      case 'under_review':
        return AppTheme.gold;
      default:
        return AppTheme.textSecondary;
    }
  }
}

// ═══════════════════════════════════════════════════════
// PULSING ICON (pendant l'enregistrement)
// ═══════════════════════════════════════════════════════

class _PulsingIcon extends StatefulWidget {
  @override
  State<_PulsingIcon> createState() => _PulsingIconState();
}

class _PulsingIconState extends State<_PulsingIcon>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 800),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _controller,
      builder: (context, child) {
        return Transform.scale(
          scale: 1.0 + (_controller.value * 0.12),
          child: Icon(
            Icons.mic_rounded,
            size: 48,
            color: Colors.red.withOpacity(0.5 + _controller.value * 0.5),
          ),
        );
      },
    );
  }
}

// ═══════════════════════════════════════════════════════
// INFO BADGE
// ═══════════════════════════════════════════════════════

class _InfoBadge extends StatelessWidget {
  final IconData icon;
  final String label;

  const _InfoBadge({required this.icon, required this.label});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: AppTheme.cardDark,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: AppTheme.textSecondary.withOpacity(0.15)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: AppTheme.gold),
          const SizedBox(width: 6),
          Text(
            label,
            style: GoogleFonts.inter(
              fontSize: 11,
              fontWeight: FontWeight.w500,
              color: AppTheme.textSecondary,
            ),
          ),
        ],
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
