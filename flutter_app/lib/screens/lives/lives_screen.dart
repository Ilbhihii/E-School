import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';

import '../../config/theme.dart';
import '../../models/live.dart';
import '../../services/api_client.dart';
import '../../widgets/loading_widget.dart';
import '../../widgets/error_widget.dart';

class LivesScreen extends StatefulWidget {
  const LivesScreen({super.key});

  @override
  State<LivesScreen> createState() => _LivesScreenState();
}

class _LivesScreenState extends State<LivesScreen>
    with SingleTickerProviderStateMixin {
  final ApiClient _api = ApiClient();
  late TabController _tabController;

  List<LiveSession> _allLives = [];
  List<LiveSession> _myLives = [];
  List<LiveSession> _upcomingLives = [];
  bool _isLoading = true;
  bool _isLoadingMyLives = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _loadLives();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadLives() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final results = await Future.wait([
      _api.get('/lives'),
      _api.get('/lives/upcoming'),
    ]);

    final allResponse = results[0];
    final upcomingResponse = results[1];

    if (allResponse.success && upcomingResponse.success) {
      final allList = (allResponse.data is Map
              ? (allResponse.data as Map)['data']
              : allResponse.data) as List?;
      final upcomingList = (upcomingResponse.data is Map
              ? (upcomingResponse.data as Map)['data']
              : upcomingResponse.data) as List?;

      setState(() {
        _allLives = (allList ?? [])
            .map((e) => LiveSession.fromJson(e as Map<String, dynamic>))
            .toList();
        _upcomingLives = (upcomingList ?? [])
            .map((e) => LiveSession.fromJson(e as Map<String, dynamic>))
            .toList();
        _isLoading = false;
      });
    } else {
      setState(() {
        _error = allResponse.message ?? upcomingResponse.message ?? 'Erreur de chargement.';
        _isLoading = false;
      });
    }
  }

  Future<void> _loadMyLives() async {
    setState(() => _isLoadingMyLives = true);

    final response = await _api.get('/user/lives');
    if (response.success) {
      final list = (response.data is Map
              ? (response.data as Map)['data']
              : response.data) as List?;
      setState(() {
        _myLives = (list ?? [])
            .map((e) => LiveSession.fromJson(e as Map<String, dynamic>))
            .toList();
        _isLoadingMyLives = false;
      });
    } else {
      setState(() {
        _isLoadingMyLives = false;
      });
    }
  }

  void _joinLive(LiveSession live) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Rejoindre le live'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(live.title, style: GoogleFonts.inter(fontWeight: FontWeight.w600)),
            const SizedBox(height: 8),
            if (live.provider != null)
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: AppTheme.navyBlue.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  live.provider!.toUpperCase(),
                  style: GoogleFonts.inter(
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.navyBlue,
                  ),
                ),
              ),
            const SizedBox(height: 16),
            Text(
              live.streamUrl != null
                  ? 'Lien de diffusion :\n${live.streamUrl}'
                  : 'Aucun lien de diffusion disponible.',
              style: GoogleFonts.inter(
                color: AppTheme.textSecondary,
                fontSize: 13,
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Fermer'),
          ),
          if (live.streamUrl != null)
            ElevatedButton.icon(
              onPressed: () {
                Navigator.of(ctx).pop();
                Clipboard.setData(ClipboardData(text: live.streamUrl!));
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Lien copié dans le presse-papier')),
                );
              },
              icon: const Icon(Icons.copy_rounded, size: 18),
              label: const Text('Copier le lien'),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.gold,
                foregroundColor: AppTheme.primaryDark,
              ),
            ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.live_tv_rounded, color: AppTheme.error, size: 22),
            const SizedBox(width: 8),
            const Text('Lives', style: TextStyle(fontWeight: FontWeight.w600)),
            if (_upcomingLives.isNotEmpty) ...[
              const SizedBox(width: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: AppTheme.error.withOpacity(0.15),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  '${_upcomingLives.length}',
                  style: GoogleFonts.inter(
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.error,
                  ),
                ),
              ),
            ],
          ],
        ),
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: AppTheme.gold,
          labelColor: AppTheme.gold,
          unselectedLabelColor: AppTheme.textSecondary,
          tabs: const [
            Tab(text: 'Tous'),
            Tab(text: 'À venir'),
            Tab(text: 'Mes lives'),
          ],
          onTap: (index) {
            if (index == 2) _loadMyLives();
          },
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppTheme.gold))
          : _error != null
              ? ErrorDisplayWidget(message: _error!, onRetry: _loadLives)
              : TabBarView(
                  controller: _tabController,
                  children: [
                    _buildAllLives(),
                    _buildUpcomingLives(),
                    _buildMyLives(),
                  ],
                ),
    );
  }

  Widget _buildAllLives() {
    if (_allLives.isEmpty) {
      return const EmptyStateWidget(message: 'Aucun live disponible pour le moment.');
    }
    return RefreshIndicator(
      onRefresh: _loadLives,
      child: ListView.builder(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.all(16),
        itemCount: _allLives.length,
        itemBuilder: (context, index) => _LiveCard(
          live: _allLives[index],
          onJoin: () => _joinLive(_allLives[index]),
        ),
      ),
    );
  }

  Widget _buildUpcomingLives() {
    if (_upcomingLives.isEmpty) {
      return const EmptyStateWidget(message: 'Aucun live à venir.');
    }
    return RefreshIndicator(
      onRefresh: _loadLives,
      child: ListView.builder(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.all(16),
        itemCount: _upcomingLives.length,
        itemBuilder: (context, index) => _LiveCard(
          live: _upcomingLives[index],
          onJoin: () => _joinLive(_upcomingLives[index]),
          isUpcoming: true,
        ),
      ),
    );
  }

  Widget _buildMyLives() {
    if (_isLoadingMyLives) {
      return const Center(child: CircularProgressIndicator(color: AppTheme.gold));
    }
    if (_myLives.isEmpty) {
      return const EmptyStateWidget(
        message: "Vous n'avez pas encore participé à des lives.",
        actionLabel: 'Explorer les lives',
        onAction: null,
      );
    }
    return RefreshIndicator(
      onRefresh: () async => _loadMyLives(),
      child: ListView.builder(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.all(16),
        itemCount: _myLives.length,
        itemBuilder: (context, index) => _LiveCard(
          live: _myLives[index],
          onJoin: () => _joinLive(_myLives[index]),
        ),
      ),
    );
  }
}

// ═══════════════════════════════════════════════════════
// LIVE CARD
// ═══════════════════════════════════════════════════════

class _LiveCard extends StatelessWidget {
  final LiveSession live;
  final VoidCallback onJoin;
  final bool isUpcoming;

  const _LiveCard({
    required this.live,
    required this.onJoin,
    this.isUpcoming = false,
  });

  String _formatDate(String? date) {
    if (date == null) return 'Date non définie';
    try {
      final parsed = DateTime.parse(date);
      return DateFormat('dd MMM yyyy', 'fr_FR').format(parsed);
    } catch (_) {
      return date;
    }
  }

  String _formatTime(String? time) {
    if (time == null) return '';
    // time is in HH:mm:ss or HH:mm format
    try {
      final parts = time.split(':');
      return '${parts[0]}h${parts[1]}';
    } catch (_) {
      return time;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: onJoin,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  // Live indicator
                  Container(
                    width: 40,
                    height: 40,
                    decoration: BoxDecoration(
                      color: (isUpcoming ? AppTheme.warning : AppTheme.error)
                          .withOpacity(0.15),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Icon(
                      isUpcoming
                          ? Icons.schedule_rounded
                          : Icons.live_tv_rounded,
                      color: isUpcoming ? AppTheme.warning : AppTheme.error,
                      size: 22,
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          live.title,
                          style: GoogleFonts.inter(
                            fontWeight: FontWeight.w600,
                            color: Colors.white,
                            fontSize: 15,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Row(
                          children: [
                            Icon(
                              Icons.calendar_today_rounded,
                              size: 12,
                              color: AppTheme.textSecondary,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              _formatDate(live.liveDate),
                              style: GoogleFonts.inter(
                                color: AppTheme.textSecondary,
                                fontSize: 12,
                              ),
                            ),
                            if (live.startTime != null) ...[
                              const SizedBox(width: 12),
                              Icon(
                                Icons.access_time_rounded,
                                size: 12,
                                color: AppTheme.textSecondary,
                              ),
                              const SizedBox(width: 4),
                              Text(
                                _formatTime(live.startTime),
                                style: GoogleFonts.inter(
                                  color: AppTheme.textSecondary,
                                  fontSize: 12,
                                ),
                              ),
                            ],
                          ],
                        ),
                      ],
                    ),
                  ),
                  // Provider badge
                  if (live.provider != null)
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: AppTheme.navyBlue.withOpacity(0.2),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        live.provider!.toUpperCase(),
                        style: GoogleFonts.inter(
                          fontSize: 9,
                          fontWeight: FontWeight.w700,
                          color: AppTheme.navyBlue,
                        ),
                      ),
                    ),
                ],
              ),
              // Class info
              if (live.classRoom != null) ...[
                const SizedBox(height: 10),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                  decoration: BoxDecoration(
                    color: AppTheme.surfaceDark,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.group_rounded,
                          size: 14, color: AppTheme.textSecondary),
                      const SizedBox(width: 6),
                      Text(
                        live.classRoom!['name'] ?? live.classRoom!['libelle'] ?? 'Toutes les classes',
                        style: GoogleFonts.inter(
                          fontSize: 12,
                          color: AppTheme.textSecondary,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
              // Join button
              const SizedBox(height: 12),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: onJoin,
                  icon: Icon(
                    isUpcoming ? Icons.notifications_active_rounded : Icons.play_arrow_rounded,
                    size: 18,
                  ),
                  label: Text(isUpcoming ? 'S\'inscrire' : 'Rejoindre'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor:
                        isUpcoming ? AppTheme.warning : AppTheme.error,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 10),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
