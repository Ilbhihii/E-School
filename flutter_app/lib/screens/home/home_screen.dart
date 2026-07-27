import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

import '../../config/theme.dart';
import '../../models/user.dart';
import '../../models/subject.dart';
import '../../services/api_client.dart';
import '../../services/auth_service.dart';
import '../../widgets/loading_widget.dart';
import '../../widgets/error_widget.dart';
import '../subjects/subjects_tab.dart';
import '../subjects/subject_levels_screen.dart';
import '../lives/lives_screen.dart';
import '../appointments/appointments_screen.dart';
import '../vocal_test/vocal_test_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _currentIndex = 0;

  late final List<Widget> _screens = [
    _DashboardTab(),
    SubjectsTab(),
    const LivesScreen(),
    const AppointmentsScreen(),
    _ProfileTab(),
  ];

  void switchToTab(int index) {
    setState(() => _currentIndex = index);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text('Smart School ',
                style: TextStyle(fontWeight: FontWeight.w300, fontSize: 18)),
            Text('Academy',
                style: GoogleFonts.poppins(
                  fontWeight: FontWeight.w800,
                  fontSize: 18,
                  color: AppTheme.gold,
                )),
          ],
        ),
        actions: [
          Consumer<AuthService>(
            builder: (context, auth, _) {
              return              IconButton(
                icon: const Icon(Icons.logout_rounded),
                onPressed: () async {
                  await auth.logout();
                  // AuthGate dans main.dart gère automatiquement la redirection
                },
                tooltip: 'Déconnexion',
              );
            },
          ),
        ],
      ),
      body: _screens[_currentIndex],
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          border: Border(
            top: BorderSide(color: AppTheme.cardDark.withOpacity(0.5)),
          ),
        ),
        child: BottomNavigationBar(
          currentIndex: _currentIndex,
          onTap: (i) => setState(() => _currentIndex = i),
          items: const [
            BottomNavigationBarItem(
              icon: Icon(Icons.dashboard_rounded),
              label: 'Accueil',
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.menu_book_rounded),
              label: 'Matières',
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.live_tv_rounded),
              label: 'Lives',
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.calendar_month_rounded),
              label: 'RDV',
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.person_rounded),
              label: 'Profil',
            ),
          ],
        ),
      ),
    );
  }
}

// ═══════════════════════════════════════════════════════
// DASHBOARD TAB
// ═══════════════════════════════════════════════════════

class _DashboardTab extends StatefulWidget {
  const _DashboardTab();

  @override
  State<_DashboardTab> createState() => _DashboardTabState();
}

class _DashboardTabState extends State<_DashboardTab> {
  final ApiClient _api = ApiClient();
  Map<String, dynamic>? _dashboardData;
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadDashboard();
  }

  Future<void> _loadDashboard() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final response = await _api.get('/dashboard');
    if (response.success) {
      setState(() {
        _dashboardData = response.data;
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
      return ErrorDisplayWidget(message: _error!, onRetry: _loadDashboard);
    }

    final stats = _dashboardData?['stats'] as Map<String, dynamic>?;
    final user = _dashboardData?['user'] as Map<String, dynamic>?;
    final subjects = (_dashboardData?['available_subjects'] as List?) ?? [];
    final upcomingLives = (_dashboardData?['upcoming_lives'] as List?) ?? [];

    return RefreshIndicator(
      onRefresh: _loadDashboard,
      child: SingleChildScrollView(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // ─── Message de bienvenue ───
            Text(
              'Bonjour, ${user?['name'] ?? 'Étudiant'} 👋',
              style: Theme.of(context).textTheme.headlineSmall,
            ),
            const SizedBox(height: 6),
            Text(
              'Continuez votre apprentissage !',
              style: Theme.of(context).textTheme.bodyMedium,
            ),
            const SizedBox(height: 24),

            // ─── Statistiques ───
            Row(
              children: [
                _StatCard(
                  title: 'Progression',
                  value: '${stats?['completion_percentage'] ?? 0}%',
                  icon: Icons.trending_up_rounded,
                  color: AppTheme.success,
                ),
                const SizedBox(width: 12),
                _StatCard(
                  title: 'Cours suivis',
                  value: '${stats?['completed_courses'] ?? 0}',
                  subtitle: '/${stats?['total_courses'] ?? 0}',
                  icon: Icons.check_circle_rounded,
                  color: AppTheme.gold,
                ),
              ],
            ),
            const SizedBox(height: 24),

            // ─── Matières disponibles ───
            Text('Matières', style: Theme.of(context).textTheme.titleLarge),
            const SizedBox(height: 12),
            ...subjects.map((s) => _SubjectCard(
              name: s['name'] ?? '',
              type: s['type'] ?? '',
              coursesCount: s['courses_count'] ?? 0,
              onTap: () {
                final subject = Subject(
                  id: s['id'] ?? 0,
                  name: s['name'] ?? '',
                  type: s['type'] ?? 'scolaire',
                  coursesCount: s['courses_count'] ?? 0,
                  levelsCount: 0,
                );
                Navigator.of(context).push(
                  MaterialPageRoute(
                    builder: (_) => SubjectLevelsScreen(subject: subject),
                  ),
                );
              },
            )),
            const SizedBox(height: 24),

            // ─── Lives à venir ───
            if (upcomingLives.isNotEmpty) ...[
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text('Lives à venir', style: Theme.of(context).textTheme.titleLarge),
                  TextButton(
                    onPressed: () {
                      final homeState = context.findAncestorStateOfType<_HomeScreenState>();
                      homeState?.switchToTab(2);
                    },
                    child: const Text('Voir tout',
                        style: TextStyle(color: AppTheme.gold)),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              ...upcomingLives.take(2).map((l) => _UpcomingLiveCard(
                title: l['title'] ?? '',
                date: l['live_date'] ?? '',
                time: l['start_time'] ?? '',
              )),
            ],
            const SizedBox(height: 24),

            // ─── Services ───
            Text('Services', style: Theme.of(context).textTheme.titleLarge),
            const SizedBox(height: 12),
            Card(
              child: InkWell(
                borderRadius: BorderRadius.circular(16),
                onTap: () {
                  Navigator.of(context).push(
                    MaterialPageRoute(
                      builder: (_) => const VocalTestScreen(),
                    ),
                  );
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
                        child: const Icon(Icons.mic_rounded,
                            color: AppTheme.purple, size: 28),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('Test vocal',
                                style: GoogleFonts.poppins(
                                  fontWeight: FontWeight.w600,
                                  color: Colors.white,
                                  fontSize: 16,
                                )),
                            const SizedBox(height: 4),
                            Text(
                              'Récitez et évaluez votre prononciation',
                              style: GoogleFonts.inter(
                                color: AppTheme.textSecondary,
                                fontSize: 13,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Icon(Icons.chevron_right_rounded,
                          color: AppTheme.textSecondary.withOpacity(0.5)),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ═══════════════════════════════════════════════════════
// PROFILE TAB
// ═══════════════════════════════════════════════════════

class _ProfileTab extends StatelessWidget {
  const _ProfileTab();

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthService>();
    final user = auth.currentUser;

    if (user == null) {
      return const Center(child: CircularProgressIndicator());
    }

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        children: [
          const SizedBox(height: 24),
          // Avatar
          CircleAvatar(
            radius: 48,
            backgroundColor: AppTheme.navyBlue,
            child: Text(
              user.name.isNotEmpty ? user.name[0].toUpperCase() : '?',
              style: GoogleFonts.poppins(
                fontSize: 36,
                fontWeight: FontWeight.w700,
                color: Colors.white,
              ),
            ),
          ),
          const SizedBox(height: 16),
          Text(user.name, style: Theme.of(context).textTheme.headlineSmall),
          const SizedBox(height: 4),
          Text(user.email, style: Theme.of(context).textTheme.bodyMedium),
          const SizedBox(height: 24),

          // Infos
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  _InfoRow(label: 'Rôle', value: user.role),
                  const Divider(color: AppTheme.cardDark),
                  _InfoRow(label: 'Compte actif', value: user.isActive ? 'Oui' : 'Non'),
                  if (user.className != null) ...[
                    const Divider(color: AppTheme.cardDark),
                    _InfoRow(label: 'Classe', value: user.className!),
                  ],
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// ═══════════════════════════════════════════════════════
// WIDGETS
// ═══════════════════════════════════════════════════════

class _StatCard extends StatelessWidget {
  final String title;
  final String value;
  final String? subtitle;
  final IconData icon;
  final Color color;

  const _StatCard({
    required this.title,
    required this.value,
    this.subtitle,
    required this.icon,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: Card(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: color.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Icon(icon, color: color, size: 20),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Row(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(value,
                      style: GoogleFonts.poppins(
                        fontSize: 28,
                        fontWeight: FontWeight.w700,
                        color: Colors.white,
                      )),
                  if (subtitle != null) ...[
                    const SizedBox(width: 2),
                    Padding(
                      padding: const EdgeInsets.only(bottom: 4),
                      child: Text(subtitle!,
                          style: GoogleFonts.inter(
                            color: AppTheme.textSecondary,
                            fontSize: 14,
                          )),
                    ),
                  ],
                ],
              ),
              const SizedBox(height: 4),
              Text(title,
                  style: GoogleFonts.inter(
                    color: AppTheme.textSecondary,
                    fontSize: 12,
                  )),
            ],
          ),
        ),
      ),
    );
  }
}

class _SubjectCard extends StatelessWidget {
  final String name;
  final String type;
  final int coursesCount;
  final VoidCallback onTap;

  const _SubjectCard({
    required this.name,
    required this.type,
    required this.coursesCount,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final isReligious = type == 'religieux';
    return Card(
      margin: const EdgeInsets.only(bottom: 8),
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
                  color: (isReligious ? AppTheme.purple : AppTheme.navyBlue).withOpacity(0.2),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(
                  isReligious ? Icons.mosque_rounded : Icons.translate_rounded,
                  color: isReligious ? AppTheme.purple : AppTheme.navyBlue,
                  size: 28,
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(name,
                        style: GoogleFonts.poppins(
                          fontWeight: FontWeight.w600,
                          color: Colors.white,
                          fontSize: 16,
                        )),
                    const SizedBox(height: 4),
                    Text(
                      '$coursesCount cours disponibles',
                      style: GoogleFonts.inter(
                        color: AppTheme.textSecondary,
                        fontSize: 13,
                      ),
                    ),
                  ],
                ),
              ),
              Icon(Icons.chevron_right_rounded,
                  color: AppTheme.textSecondary.withOpacity(0.5)),
            ],
          ),
        ),
      ),
    );
  }
}

class _UpcomingLiveCard extends StatelessWidget {
  final String title;
  final String date;
  final String time;

  const _UpcomingLiveCard({
    required this.title,
    required this.date,
    required this.time,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 8),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: AppTheme.error.withOpacity(0.15),
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Icon(Icons.live_tv_rounded, color: AppTheme.error, size: 24),
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
                      )),
                  const SizedBox(height: 4),
                  Text(
                    '$date à $time',
                    style: GoogleFonts.inter(
                      color: AppTheme.textSecondary,
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ),
            Container(
              width: 10,
              height: 10,
              decoration: const BoxDecoration(
                color: AppTheme.error,
                shape: BoxShape.circle,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _InfoRow extends StatelessWidget {
  final String label;
  final String value;

  const _InfoRow({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label,
              style: GoogleFonts.inter(
                color: AppTheme.textSecondary,
                fontSize: 14,
              )),
          Text(value,
              style: GoogleFonts.inter(
                color: Colors.white,
                fontSize: 14,
                fontWeight: FontWeight.w500,
              )),
        ],
      ),
    );
  }
}
