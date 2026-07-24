# TODO - Replace CDN Chart.js with Vite-bundled Chart.js

## Steps

- [x] 1. Analyze all relevant files (dashboard/index.blade.php, app.js, app.blade.php, package.json, vite.config.js)
- [x] 2. Create plan and get user approval
- [x] 3. Edit `package.json` - Add chart.js to dependencies
- [x] 4. Edit `resources/js/app.js` - Import Chart.js and attach to window.Chart
- [x] 5. Edit `resources/views/dashboard/index.blade.php` - Remove CDN script tag
- [x] 6. Edit `resources/views/layouts/app.blade.php` - Add @vite() directive
- [x] 7. Run `npm install` to install chart.js (2 packages added: chart.js + chart.js/auto deps)
- [x] 8. Run `npm run build` to generate manifest.json and compiled assets ✓
- [x] 9. Verify the build output and test ✓
