# Fix Tailwind CDN → PostCSS Build

## Steps
- [x] 1. Search for CDN references
- [x] 2. Read relevant files (admin layout, webpack.mix.js, tailwind.config.js, app.css)
- [x] 3. Create plan and get approval
- [x] 4. Edit `resources/views/layouts/admin.blade.php` — replace CDN script with compiled CSS link
- [x] 5. Move admin custom styles from inline `<style>` to `resources/css/app.css`
- [x] 6. Run `npm run dev` to recompile assets
- [x] 7. Verify no CDN references remain in any Blade template

