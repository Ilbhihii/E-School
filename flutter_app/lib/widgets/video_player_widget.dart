import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:youtube_player_flutter/youtube_player_flutter.dart';

import '../config/theme.dart';

/// Widget intelligent qui détecte automatiquement le type de vidéo :
/// - YouTube → lecteur intégré youtube_player_flutter
/// - URL directe .mp4 / fichier local → fallback avec ouverture externe
/// - null → placeholder gris
class VideoPlayerWidget extends StatefulWidget {
  /// URL de la vidéo (YouTube, .mp4, ou lien direct)
  final String? videoUrl;

  /// URL d'un fichier vidéo stocké localement
  final String? videoPath;

  /// URL externe du cours
  final String? courseLink;

  const VideoPlayerWidget({
    super.key,
    this.videoUrl,
    this.videoPath,
    this.courseLink,
  });

  @override
  State<VideoPlayerWidget> createState() => _VideoPlayerWidgetState();
}

class _VideoPlayerWidgetState extends State<VideoPlayerWidget> {
  YoutubePlayerController? _youtubeController;
  bool _isYouTube = false;
  bool _showPlayer = false;

  @override
  void initState() {
    super.initState();
    _detectVideoType();
  }

  void _detectVideoType() {
    final url = widget.videoUrl;
    if (url == null || url.isEmpty) return;

    // Détection YouTube
    final youtubeId = YoutubePlayer.convertUrlToId(url);
    if (youtubeId != null) {
      _isYouTube = true;
      _youtubeController = YoutubePlayerController(
        initialVideoId: youtubeId,
        flags: const YoutubePlayerFlags(
          autoPlay: false,
          mute: false,
          controlsVisibleAtStart: true,
          hideThumbnail: false,
          enableCaption: false,
        ),
      );
      setState(() {});
    }
  }

  @override
  void dispose() {
    _youtubeController?.dispose();
    super.dispose();
  }

  /// Ouvrir la vidéo dans le navigateur externe
  Future<void> _openExternally(String url) async {
    final uri = Uri.tryParse(url);
    if (uri != null && await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  @override
  Widget build(BuildContext context) {
    // Pas de vidéo du tout
    if (widget.videoUrl == null && widget.videoPath == null) {
      return const SizedBox.shrink();
    }

    return Card(
      clipBehavior: Clip.antiAlias,
      child: _isYouTube ? _buildYouTubePlayer() : _buildExternalPlayer(),
    );
  }

  /// Lecteur YouTube intégré
  Widget _buildYouTubePlayer() {
    if (!_showPlayer) {
      return _buildThumbnailOverlay(
        onPlay: () => setState(() => _showPlayer = true),
      );
    }

    return Column(
      children: [
        YoutubePlayer(
          controller: _youtubeController!,
          showVideoProgressIndicator: true,
          progressIndicatorColor: AppTheme.gold,
          progressColors: const ProgressBarColors(
            playedColor: AppTheme.gold,
            handleColor: AppTheme.gold,
          ),
          onReady: () {
            // Le joueur est prêt
          },
        ),
        // Barre d'info sous la vidéo
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
          color: AppTheme.cardDark,
          child: Row(
            children: [
              const Icon(Icons.ondemand_video_rounded,
                  size: 16, color: AppTheme.error),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  'Regarder sur YouTube',
                  style: GoogleFonts.inter(
                    color: AppTheme.textSecondary,
                    fontSize: 12,
                  ),
                ),
              ),
              GestureDetector(
                onTap: () => _openExternally(widget.videoUrl!),
                child: const Icon(Icons.open_in_new_rounded,
                    size: 16, color: AppTheme.textSecondary),
              ),
            ],
          ),
        ),
      ],
    );
  }

  /// Miniature avec bouton play pour YouTube
  Widget _buildThumbnailOverlay({required VoidCallback onPlay}) {
    final videoId = YoutubePlayer.convertUrlToId(widget.videoUrl!);
    final thumbnailUrl = videoId != null
        ? 'https://img.youtube.com/vi/$videoId/hqdefault.jpg'
        : null;

    return InkWell(
      onTap: onPlay,
      child: Container(
        height: 220,
        decoration: BoxDecoration(
          color: AppTheme.cardDark,
          image: thumbnailUrl != null
              ? DecorationImage(
                  image: NetworkImage(thumbnailUrl),
                  fit: BoxFit.cover,
                  onError: (_, __) {},
                )
              : null,
        ),
        child: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: [
                Colors.transparent,
                Colors.black.withOpacity(0.3),
              ],
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
            ),
          ),
          child: Center(
            child: Container(
              width: 68,
              height: 68,
              decoration: BoxDecoration(
                color: AppTheme.gold.withOpacity(0.95),
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: AppTheme.gold.withOpacity(0.4),
                    blurRadius: 25,
                    spreadRadius: 2,
                  ),
                ],
              ),
              child: const Icon(Icons.play_arrow_rounded,
                  color: AppTheme.primaryDark, size: 40),
            ),
          ),
        ),
      ),
    );
  }

  /// Lecteur externe pour les URLs non-YouTube (MP4, Vimeo, lien direct)
  Widget _buildExternalPlayer() {
    final url = widget.videoUrl ?? widget.videoPath ?? widget.courseLink ?? '';

    return InkWell(
      onTap: () => _openExternally(url),
      child: Container(
        height: 200,
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: [
              AppTheme.navyBlue.withOpacity(0.3),
              AppTheme.surfaceDark,
            ],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 60,
              height: 60,
              decoration: BoxDecoration(
                color: AppTheme.navyBlue.withOpacity(0.8),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.open_in_browser_rounded,
                  color: Colors.white, size: 30),
            ),
            const SizedBox(height: 12),
            Text(
              'Ouvrir la vidéo',
              style: GoogleFonts.inter(
                color: Colors.white,
                fontSize: 15,
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              'Lire dans le navigateur',
              style: GoogleFonts.inter(
                color: AppTheme.textSecondary,
                fontSize: 12,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
