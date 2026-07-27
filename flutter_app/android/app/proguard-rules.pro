# Flutter specific rules
-keep class io.flutter.** { *; }
-keep class io.flutter.plugins.** { *; }
-dontwarn io.flutter.**
-dontwarn com.google.errorprone.**

# Firebase / FCM
-keep class com.google.firebase.** { *; }
-keep class com.google.android.gms.** { *; }
-dontwarn com.google.firebase.**
-dontwarn com.google.android.gms.**

# Dio & Retrofit
-keep class com.dio.** { *; }
-dontwarn com.dio.**

# Gson / JSON serialization
-keepattributes Signature
-keepattributes *Annotation*
-keep class com.smartschool.academy.model.** { *; }
-keepclassmembers class * {
    @com.google.gson.annotations.SerializedName <fields>;
}

# Keep model classes used for JSON parsing
-keep class com.smartschool.academy.models.** { *; }
-keepclassmembers class com.smartschool.academy.models.** { *; }

# Keep enum classes
-keepclassmembers enum * {
    public static **[] values();
    public static ** valueOf(java.lang.String);
}

# Keep Parcelable
-keepclassmembers class * implements android.os.Parcelable {
    public static final android.os.Parcelable$Creator CREATOR;
}
