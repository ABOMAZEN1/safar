<?php
// ... existing code ...
?><style>
  [dir="rtl"] .fi-topbar nav, 
  [dir="rtl"] .fi-breadcrumbs { direction: rtl; }

  /* قلب محاذاة السايدبار لليمين بدون تغيير شبكات الأعمدة */
  [dir="rtl"] .fi-sidebar { inset-inline-start: auto !important; inset-inline-end: 0 !important; }
  [dir="rtl"] .fi-sidebar .fi-sidebar-nav { padding-inline: 0.5rem; }
  [dir="rtl"] .fi-sidebar .fi-sidebar-item { text-align: right; }
  [dir="rtl"] .fi-sidebar .fi-sidebar-item-icon { margin-left: 0.5rem; margin-right: 0 !important; }

  /* انعكاس مسافات العناصر داخل الصفحة */
  [dir="rtl"] .rtl\:space-x-reverse > :not([hidden]) ~ :not([hidden]) {
    --tw-space-x-reverse: 1;
  }
</style>
<script>
  document.documentElement.setAttribute('dir', 'rtl');
  document.documentElement.setAttribute('lang', 'ar');
  document.addEventListener('alpine:init', () => {
    document.documentElement.setAttribute('dir', 'rtl');
  });
</script>
