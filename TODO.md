# Remove Captcha from Login TODO

- [ ] 1. Update TODO.md with new plan
- [x] 2. Edit app/Http/Requests/Auth/LoginRequest.php - remove captcha_answer rule\n- [x] 3. Edit app/Http/Controllers/Auth/AuthenticatedSessionController.php - remove captcha generation
- [ ] 4. Edit resources/js/pages/auth/Login.vue - remove captcha UI
- [ ] 5. Edit resources/js/pages/Welcome.vue if needed
- [ ] 6. Clear caches
- [ ] 7. Test login (email/password only)

