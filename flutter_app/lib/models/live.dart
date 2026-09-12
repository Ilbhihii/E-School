class LiveSession {
  final int id;
  final String title;
  final String? streamUrl;
  final String? provider;
  final String? liveDate;
  final String? startTime;
  final String? endTime;
  final Map<String, dynamic>? classRoom;
  final Map<String, dynamic>? user;
  final String? teamsAppUrl;
  final bool canJoin;
  final String? joinEndpoint;
  final String? accessMessage;
  final DateTime? createdAt;

  LiveSession({
    required this.id,
    required this.title,
    this.streamUrl,
    this.provider,
    this.liveDate,
    this.startTime,
    this.endTime,
    this.classRoom,
    this.user,
    this.teamsAppUrl,
    this.canJoin = false,
    this.joinEndpoint,
    this.accessMessage,
    this.createdAt,
  });

  factory LiveSession.fromJson(Map<String, dynamic> json) {
    return LiveSession(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      streamUrl: json['stream_url'],
      provider: json['provider'],
      liveDate: json['live_date'],
      startTime: json['start_time'],
      endTime: json['end_time'],
      classRoom: json['class'],
      user: json['user'],
      teamsAppUrl: json['teams_app_url'],
      canJoin: json['can_join'] == true,
      joinEndpoint: json['join_endpoint'],
      accessMessage: json['access_message'],
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at']) : null,
    );
  }

  bool get isUpcoming {
    if (liveDate == null) return false;
    final date = DateTime.tryParse(liveDate!);
    if (date == null) return false;
    return date.isAfter(DateTime.now()) || date.isAtSameMomentAs(DateTime.now());
  }
}
