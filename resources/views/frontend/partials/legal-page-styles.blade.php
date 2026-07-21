/* ═══════════════ Legal pages — emerald & slate ═══════════════ */

.lg-hero {
  position: relative;
  overflow: hidden;
  margin-top: 84px;
  padding: 56px 0 50px;
  background:
    radial-gradient(circle at 92% 6%, rgba(14, 159, 110, 0.5), transparent 40%),
    radial-gradient(circle at 4% 100%, rgba(245, 165, 36, 0.14), transparent 32%),
    linear-gradient(120deg, #0d1b16 0%, #06432f 100%);
  color: #ffffff;
}

.lg-hero-dots {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1.5px, transparent 1.5px);
  background-size: 22px 22px;
  mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
  -webkit-mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
  pointer-events: none;
}

.lg-hero .container {
  position: relative;
  z-index: 1;
}

.lg-breadcrumb {
  display: flex;
  align-items: center;
  gap: 9px;
  margin-bottom: 16px;
  color: #a9c7ba;
  font-size: 12.5px;
  font-weight: 700;
}

.lg-breadcrumb a {
  color: #dbeee3;
  text-decoration: none;
}

.lg-breadcrumb a:hover {
  color: #ffffff;
}

.lg-breadcrumb i {
  font-size: 10px;
  color: #6f9a84;
}

.lg-breadcrumb span {
  color: #8be3bf;
}

.lg-hero h1 {
  margin: 0 0 8px;
  color: #ffffff;
  font-size: clamp(28px, 3.4vw, 40px);
  font-weight: 900;
  letter-spacing: -0.015em;
}

.lg-hero p {
  margin: 0;
  color: #bcd8cb;
  font-size: 13.5px;
}

.lg-content-section {
  padding: 60px 0 90px;
  background: #f4f7f5;
}

.legal-page-content {
  padding: 44px 48px;
  background: #ffffff;
  border-radius: 22px;
  box-shadow: 0 28px 64px rgba(16, 29, 44, 0.1);
}

.legal-page-content h1,
.legal-page-content h2,
.legal-page-content h3 {
  color: #101d2c;
  line-height: 1.28;
  font-weight: 900;
}

.legal-page-content h1 {
  margin-bottom: 14px;
  font-size: 30px;
}

.legal-page-content h2 {
  margin: 34px 0 14px;
  padding-top: 24px;
  border-top: 1px dashed #e4e9e6;
  font-size: 20px;
}

.legal-page-content h2:first-of-type {
  padding-top: 0;
  border-top: 0;
}

.legal-page-content h3 {
  margin: 22px 0 10px;
  font-size: 16px;
}

.legal-page-content p,
.legal-page-content li {
  color: #4b5768;
  font-size: 15px;
  line-height: 1.8;
}

.legal-page-content ul {
  padding-left: 22px;
  margin-bottom: 18px;
}

.legal-page-content li {
  margin-bottom: 6px;
}

.legal-page-content a {
  color: #0a7d57;
  font-weight: 700;
}

.legal-page-content a:hover {
  color: #0e9f6e;
}

.legal-page-content strong {
  color: #101d2c;
}

.lg-updated-chip {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 22px;
  padding: 7px 14px;
  color: #0a7d57;
  background: #e5f5ee;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 800;
}

@media (max-width: 767.98px) {
  .lg-hero {
    padding: 40px 0 36px;
  }

  .lg-content-section {
    padding: 40px 0 60px;
  }

  .legal-page-content {
    padding: 28px 22px;
  }
}
