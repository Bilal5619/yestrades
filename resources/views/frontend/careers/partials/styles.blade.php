/* ═══════════════ Careers pages — emerald & slate ═══════════════ */

.cr-page,
.career-detail,
.cr-form-page,
.cr-success-page {
  --cr-primary: #0e9f6e;
  --cr-primary-dark: #0a7d57;
  --cr-primary-deep: #06432f;
  --cr-primary-soft: #e5f5ee;
  --cr-amber: #f5a524;
  --cr-ink: #101d2c;
  --cr-ink-2: #33415c;
  --cr-muted: #5e6b7e;
  --cr-line: #e4e9e6;
  --cr-bg: #f4f7f5;
  --cr-radius: 18px;
  --cr-shadow: 0 14px 40px rgba(16, 29, 44, 0.07);
  --cr-shadow-lg: 0 28px 64px rgba(16, 29, 44, 0.13);
  background: #ffffff;
}

/* ── Hero ─────────────────────────────────────── */
.cr-hero {
  position: relative;
  overflow: hidden;
  margin-top: 84px;
  padding: 80px 0 70px;
  background:
    radial-gradient(circle at 92% 6%, rgba(14, 159, 110, 0.5), transparent 40%),
    radial-gradient(circle at 4% 100%, rgba(245, 165, 36, 0.14), transparent 32%),
    linear-gradient(120deg, #0d1b16 0%, var(--cr-primary-deep) 100%);
  color: #ffffff;
}

.cr-hero-blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(85px);
  pointer-events: none;
}

.cr-hero-blob-1 {
  top: -140px;
  right: -100px;
  width: 440px;
  height: 440px;
  background: rgba(14, 159, 110, 0.32);
}

.cr-hero-blob-2 {
  bottom: -160px;
  left: -120px;
  width: 400px;
  height: 400px;
  background: rgba(245, 165, 36, 0.1);
}

.cr-hero-dots {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(255, 255, 255, 0.06) 1.5px, transparent 1.5px);
  background-size: 22px 22px;
  mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
  -webkit-mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
  pointer-events: none;
}

.cr-hero .container {
  position: relative;
  z-index: 1;
}

.cr-hero-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.1fr) minmax(280px, 0.9fr);
  align-items: center;
  gap: 50px;
}

.cr-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  margin-bottom: 20px;
  padding: 8px 15px;
  color: #dbeee3;
  background: rgba(255, 255, 255, 0.07);
  border: 1px solid rgba(255, 255, 255, 0.14);
  border-radius: 999px;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

.cr-eyebrow-dot {
  width: 8px;
  height: 8px;
  background: #22d98b;
  border-radius: 50%;
  box-shadow: 0 0 0 4px rgba(34, 217, 139, 0.18);
}

.cr-hero h1 {
  margin: 0 0 16px;
  color: #ffffff;
  font-size: clamp(30px, 3.6vw, 44px);
  line-height: 1.12;
  font-weight: 900;
  letter-spacing: -0.015em;
}

.cr-hero h1 span {
  display: block;
  color: #7fe0b9;
}

.cr-hero p {
  max-width: 520px;
  margin: 0;
  color: #bcd8cb;
  font-size: 15px;
  line-height: 1.75;
}

.cr-hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 28px;
}

.cr-btn-primary,
.cr-btn-ghost {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 9px;
  min-height: 48px;
  padding: 0 22px;
  border-radius: 999px;
  font-size: 14px;
  font-weight: 800;
  text-decoration: none;
  border: 0;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease, border-color 0.2s ease;
}

.cr-btn-primary {
  color: #ffffff;
  background: linear-gradient(120deg, var(--cr-primary), var(--cr-primary-dark));
  box-shadow: 0 14px 30px rgba(14, 159, 110, 0.3);
}

.cr-btn-primary:hover {
  color: #ffffff;
  filter: brightness(1.06);
  transform: translateY(-2px);
  box-shadow: 0 18px 38px rgba(14, 159, 110, 0.4);
}

.cr-btn-ghost {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.18);
}

.cr-btn-ghost:hover {
  color: #ffffff;
  border-color: rgba(255, 255, 255, 0.36);
  background: rgba(255, 255, 255, 0.13);
}

.cr-hero-panel {
  padding: 30px 28px;
  background: #ffffff;
  border-radius: 22px;
  box-shadow: 0 30px 70px rgba(0, 0, 0, 0.28);
}

.cr-panel-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 50px;
  height: 50px;
  margin-bottom: 18px;
  color: var(--cr-primary-dark);
  background: var(--cr-primary-soft);
  border-radius: 14px;
  font-size: 21px;
}

.cr-hero-panel h2 {
  margin: 0 0 10px;
  color: var(--cr-ink);
  font-size: 19px;
  font-weight: 900;
  letter-spacing: -0.01em;
}

.cr-hero-panel p {
  margin: 0;
  color: var(--cr-muted);
  font-size: 13.5px;
  line-height: 1.7;
}

/* ── Shared section styling ───────────────────── */
.cr-section {
  padding: 78px 0;
}

.cr-jobs-section {
  background: var(--cr-bg);
}

.cr-section-head {
  max-width: 680px;
  margin: 0 auto 40px;
  text-align: center;
}

.cr-head-split {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
  max-width: 100%;
  text-align: left;
  margin-bottom: 34px;
}

.cr-tag {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 14px;
  padding: 8px 15px;
  color: var(--cr-primary-dark);
  background: var(--cr-primary-soft);
  border-radius: 999px;
  font-size: 11.5px;
  font-weight: 900;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.cr-section-head h2 {
  margin: 0;
  color: var(--cr-ink);
  font-size: clamp(26px, 3vw, 34px);
  line-height: 1.16;
  font-weight: 900;
  letter-spacing: -0.015em;
}

/* ── Why work with us cards ───────────────────── */
.cr-why-card {
  height: 100%;
  padding: 28px 24px;
  background: #ffffff;
  border: 1px solid var(--cr-line);
  border-radius: var(--cr-radius);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.cr-why-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--cr-shadow-lg);
}

.cr-why-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 50px;
  height: 50px;
  margin-bottom: 18px;
  color: var(--cr-primary-dark);
  background: var(--cr-primary-soft);
  border-radius: 14px;
  font-size: 20px;
}

.cr-why-card h3 {
  margin: 0 0 8px;
  color: var(--cr-ink);
  font-size: 16.5px;
  font-weight: 800;
}

.cr-why-card p {
  margin: 0;
  color: var(--cr-muted);
  font-size: 13.5px;
  line-height: 1.65;
}

/* ── Filter form ───────────────────────────────── */
.cr-filter-card {
  margin-bottom: 34px;
  padding: 24px;
  background: #ffffff;
  border: 1px solid var(--cr-line);
  border-radius: var(--cr-radius);
  box-shadow: var(--cr-shadow);
}

.cr-filter-card label {
  display: block;
  margin-bottom: 7px;
  color: var(--cr-ink-2);
  font-size: 11.5px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.cr-filter-actions {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-top: 4px;
}

.cr-filter-clear {
  color: var(--cr-muted);
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
}

.cr-filter-clear:hover {
  color: var(--cr-primary-dark);
}

/* ── Job cards ─────────────────────────────────── */
.cr-job-card {
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 26px;
  background: #ffffff;
  border: 1px solid var(--cr-line);
  border-radius: var(--cr-radius);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.cr-job-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--cr-shadow-lg);
}

.cr-job-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px 14px;
  margin-bottom: 14px;
  color: var(--cr-muted);
  font-size: 12px;
  font-weight: 700;
}

.cr-job-tags i {
  margin-right: 5px;
  color: var(--cr-primary);
}

.cr-job-card h3 {
  margin: 0 0 12px;
  font-size: 18px;
  font-weight: 900;
  line-height: 1.35;
}

.cr-job-card h3 a {
  color: var(--cr-ink);
  text-decoration: none;
}

.cr-job-card h3 a:hover {
  color: var(--cr-primary-dark);
}

.cr-job-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 14px;
}

.cr-job-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 11px;
  color: var(--cr-primary-dark);
  background: var(--cr-primary-soft);
  border-radius: 999px;
  font-size: 11.5px;
  font-weight: 800;
}

.cr-job-salary {
  margin: 0 0 10px;
  color: var(--cr-ink);
  font-size: 15px;
  font-weight: 800;
}

.cr-job-summary {
  margin: 0 0 14px;
  color: var(--cr-muted);
  font-size: 13.5px;
  line-height: 1.65;
}

.cr-job-closing {
  margin: 0 0 16px;
  color: var(--cr-muted);
  font-size: 12px;
  font-weight: 600;
}

.cr-job-closing i {
  margin-right: 6px;
  color: var(--cr-amber);
}

.cr-job-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: auto;
  padding: 11px 18px;
  color: var(--cr-primary-dark);
  background: var(--cr-primary-soft);
  border-radius: 999px;
  font-size: 13px;
  font-weight: 800;
  text-decoration: none;
  transition: all 0.2s ease;
}

.cr-job-btn:hover {
  color: #ffffff;
  background: var(--cr-primary);
  box-shadow: 0 10px 22px rgba(14, 159, 110, 0.32);
}

/* ── Empty state ──────────────────────────────── */
.cr-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  padding: 70px 24px;
  text-align: center;
  background: #ffffff;
  border: 1px solid var(--cr-line);
  border-radius: var(--cr-radius);
}

.cr-empty i {
  font-size: 34px;
  color: var(--cr-primary);
}

.cr-empty h3 {
  margin: 0;
  color: var(--cr-ink);
  font-size: 19px;
  font-weight: 800;
}

.cr-empty p {
  margin: 0;
  color: var(--cr-muted);
  font-size: 14px;
}

.cr-pagination-wrap {
  margin-top: 36px;
  display: flex;
  justify-content: center;
}

.cr-pagination-wrap .page-link {
  color: var(--cr-ink-2);
  border-radius: 999px !important;
  margin: 0 3px;
  border-color: var(--cr-line);
}

.cr-pagination-wrap .page-item.active .page-link {
  color: #ffffff;
  background: var(--cr-primary);
  border-color: var(--cr-primary);
}

/* ── Job detail page ──────────────────────────── */
.career-detail {
  margin-top: 84px;
  padding: 60px 0 90px;
  background: var(--cr-bg);
}

.cr-back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: var(--cr-muted);
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
}

.cr-back-link:hover {
  color: var(--cr-primary-dark);
}

.cr-job-header {
  margin-top: 18px;
  padding: 30px;
  background: #ffffff;
  border-radius: var(--cr-radius);
  box-shadow: var(--cr-shadow);
}

.cr-job-header h1 {
  margin: 0 0 12px;
  color: var(--cr-ink);
  font-size: clamp(26px, 3vw, 34px);
  font-weight: 900;
  letter-spacing: -0.015em;
}

.cr-job-header-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 14px;
}

.cr-job-salary-lg {
  margin: 0 0 6px;
  color: var(--cr-primary-dark);
  font-size: 20px;
  font-weight: 900;
}

.cr-job-closing-lg {
  margin: 0;
  color: var(--cr-muted);
  font-size: 13px;
  font-weight: 600;
}

.cr-detail-body {
  margin-top: 24px;
  padding: 30px;
  background: #ffffff;
  border-radius: var(--cr-radius);
  box-shadow: var(--cr-shadow);
}

.cr-detail-body .lead {
  color: var(--cr-ink-2);
  font-size: 15.5px;
  line-height: 1.7;
}

.cr-detail-section {
  margin-top: 28px;
  padding-top: 24px;
  border-top: 1px dashed var(--cr-line);
}

.cr-detail-section h2 {
  margin: 0 0 12px;
  color: var(--cr-ink);
  font-size: 19px;
  font-weight: 900;
}

.cr-detail-section > div,
.cr-detail-section p {
  color: var(--cr-ink-2);
  font-size: 14px;
  line-height: 1.75;
}

.cr-apply-sidebar {
  position: sticky;
  top: 100px;
  padding: 26px;
  background: linear-gradient(135deg, #071122 0%, #06301f 100%);
  border-radius: var(--cr-radius);
  color: #ffffff;
  box-shadow: var(--cr-shadow-lg);
}

.cr-apply-sidebar h2 {
  margin: 0 0 8px;
  color: #ffffff;
  font-size: 19px;
  font-weight: 900;
}

.cr-apply-sidebar p {
  margin: 0 0 20px;
  color: #b7c4d6;
  font-size: 13.5px;
}

.cr-apply-sidebar .cr-btn-primary {
  width: 100%;
}

.cr-apply-note {
  display: flex;
  align-items: flex-start;
  gap: 9px;
  margin-top: 18px;
  padding-top: 18px;
  border-top: 1px solid rgba(255, 255, 255, 0.14);
  color: #9fb0c4;
  font-size: 12px;
  line-height: 1.6;
}

.cr-apply-note i {
  margin-top: 2px;
  color: #22d98b;
}

/* ── Application form (shared: job apply + general CV) ── */
.cr-form-page {
  margin-top: 84px;
  padding: 60px 0 90px;
  background: var(--cr-bg);
}

.cr-form-card,
#apply-now {
  padding: 30px !important;
  background: #ffffff !important;
  border: 0 !important;
  border-radius: var(--cr-radius) !important;
  box-shadow: var(--cr-shadow) !important;
}

#apply-now h2 {
  margin: 0 0 20px;
  color: var(--cr-ink);
  font-size: 20px;
  font-weight: 900;
}

.cr-form-card h1 {
  margin: 0 0 8px;
  color: var(--cr-ink);
  font-size: clamp(26px, 3vw, 32px);
  font-weight: 900;
}

.cr-form-card .lead {
  color: var(--cr-muted);
  font-size: 14.5px;
  margin-bottom: 26px;
}

/* Reskin the shared Bootstrap-style fields partial */
.cr-page label,
.career-detail label,
.cr-form-page label {
  display: block;
  margin-bottom: 7px;
  color: var(--cr-ink-2);
  font-size: 12px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.cr-page .form-control,
.career-detail .form-control,
.cr-form-page .form-control {
  min-height: 46px;
  padding: 0 15px;
  color: var(--cr-ink);
  background: var(--cr-bg);
  border: 1px solid var(--cr-line);
  border-radius: 12px;
  font-size: 14px;
  transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
}

.cr-page textarea.form-control,
.career-detail textarea.form-control,
.cr-form-page textarea.form-control {
  min-height: 0;
  padding: 12px 15px;
}

.cr-page .form-control:focus,
.career-detail .form-control:focus,
.cr-form-page .form-control:focus {
  background: #ffffff;
  border-color: rgba(14, 159, 110, 0.45);
  box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.12);
  outline: none;
}

.cr-page .form-control-file,
.career-detail .form-control-file,
.cr-form-page .form-control-file {
  display: block;
  width: 100%;
  padding: 10px 4px;
  color: var(--cr-ink-2);
  font-size: 13.5px;
}

.cr-page .form-check,
.career-detail .form-check,
.cr-form-page .form-check {
  display: flex;
  align-items: flex-start;
  gap: 9px;
  padding-left: 0;
}

.cr-page .form-check-input,
.career-detail .form-check-input,
.cr-form-page .form-check-input {
  margin: 3px 0 0;
  width: 17px;
  height: 17px;
  accent-color: var(--cr-primary);
}

.cr-page .form-check-label,
.career-detail .form-check-label,
.cr-form-page .form-check-label {
  color: var(--cr-ink-2);
  font-size: 13px;
  font-weight: 600;
  line-height: 1.6;
  text-transform: none;
  letter-spacing: normal;
}

.cr-page .form-check-label a,
.career-detail .form-check-label a,
.cr-form-page .form-check-label a {
  color: var(--cr-primary-dark);
  font-weight: 800;
}

.cr-page .invalid-feedback,
.career-detail .invalid-feedback,
.cr-form-page .invalid-feedback,
.cr-page .text-danger,
.career-detail .text-danger,
.cr-form-page .text-danger {
  color: #d33a4b !important;
  font-size: 12.5px;
  font-weight: 600;
}

.cr-page button[type="submit"],
.career-detail button[type="submit"],
.cr-form-page button[type="submit"] {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 9px;
  min-height: 50px;
  padding: 0 26px;
  color: #ffffff;
  background: linear-gradient(120deg, var(--cr-primary), var(--cr-primary-dark));
  border: 0;
  border-radius: 999px;
  font-size: 14.5px;
  font-weight: 800;
  box-shadow: 0 14px 30px rgba(14, 159, 110, 0.3);
  transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
}

.cr-page button[type="submit"]:hover,
.career-detail button[type="submit"]:hover,
.cr-form-page button[type="submit"]:hover {
  color: #ffffff;
  filter: brightness(1.06);
  transform: translateY(-2px);
  box-shadow: 0 18px 38px rgba(14, 159, 110, 0.4);
}

/* ── Success page ─────────────────────────────── */
.cr-success-page {
  margin-top: 84px;
  padding: 90px 0;
  background: var(--cr-bg);
  min-height: 60vh;
  display: flex;
  align-items: center;
}

.cr-success-card {
  max-width: 600px;
  margin: 0 auto;
  padding: 50px 40px;
  text-align: center;
  background: #ffffff;
  border-radius: 22px;
  box-shadow: var(--cr-shadow-lg);
}

.cr-success-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 74px;
  height: 74px;
  margin-bottom: 22px;
  color: var(--cr-primary-dark);
  background: var(--cr-primary-soft);
  border-radius: 50%;
  font-size: 30px;
}

.cr-success-card h1 {
  margin: 0 0 12px;
  color: var(--cr-ink);
  font-size: 26px;
  font-weight: 900;
}

.cr-success-card p {
  margin: 0 0 6px;
  color: var(--cr-muted);
  font-size: 14.5px;
  line-height: 1.7;
}

.cr-success-ref {
  display: inline-block;
  margin: 14px 0 22px;
  padding: 10px 18px;
  color: var(--cr-primary-dark);
  background: var(--cr-primary-soft);
  border-radius: 999px;
  font-size: 14px;
  font-weight: 900;
}

.cr-success-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 26px;
}

.cr-btn-outline {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 9px;
  min-height: 48px;
  padding: 0 22px;
  color: var(--cr-primary-dark);
  background: #ffffff;
  border: 1px solid var(--cr-line);
  border-radius: 999px;
  font-size: 14px;
  font-weight: 800;
  text-decoration: none;
  transition: all 0.2s ease;
}

.cr-btn-outline:hover {
  color: var(--cr-primary-dark);
  border-color: rgba(14, 159, 110, 0.4);
  background: var(--cr-primary-soft);
}

/* ── Responsive ───────────────────────────────── */
@media (max-width: 991.98px) {
  .cr-hero-grid {
    grid-template-columns: 1fr;
    gap: 32px;
  }

  .career-detail .row {
    flex-direction: column-reverse;
  }

  .cr-apply-sidebar {
    position: static;
    margin-bottom: 24px;
  }
}

@media (max-width: 767.98px) {
  .cr-hero {
    padding: 56px 0 48px;
  }

  .cr-section {
    padding: 52px 0;
  }

  .cr-job-header,
  .cr-detail-body,
  .cr-form-card,
  #apply-now {
    padding: 22px !important;
  }

  .cr-success-card {
    padding: 36px 22px;
  }
}
