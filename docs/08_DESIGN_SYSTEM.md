# Kasiva Design System & Token Specifications

## 1. Palette Warna Resmi (Kasiva Brand Palette)

| Color Token | Hex Code | Utility Name (Tailwind v4) | CSS Variable | Visual Meaning / Usage |
|---|---|---|---|---|
| **Navy** | `#272D48` | `bg-[#272D48]`, `text-white` | `--color-kasiva-navy` | Primary Dark Layout, Header Navbar, Container Headings |
| **Blue-violet** | `#505B93` | `border-[#505B93]`, `bg-[#505B93]` | `--color-kasiva-blue-violet` | Card Borders, Container Divider, Secondary Elements |
| **Teal** | `#00AAA6` | `bg-[#00AAA6]`, `text-[#00AAA6]` | `--color-kasiva-teal` | Primary Action CTA Buttons, Active Dots, Primary Accent |
| **Periwinkle** | `#8696ED` | `text-[#8696ED]` | `--color-kasiva-periwinkle` | SKU Badges, Soft Accents, Hover States |
| **Cyan-teal** | `#3EDAD7` | `text-[#3EDAD7]` | `--color-kasiva-cyan-teal` | Active Status Indicators, Price/Change Totals |

---

## 2. TailwindCSS v4 Setup (`resources/css/app.css`)

```css
@import "tailwindcss";

@theme {
  --font-sans: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;

  --color-kasiva-navy: #272D48;
  --color-kasiva-blue-violet: #505B93;
  --color-kasiva-teal: #00AAA6;
  --color-kasiva-periwinkle: #8696ED;
  --color-kasiva-cyan-teal: #3EDAD7;
}
```

---

## 3. Logo Usage Guidelines

### 3.1 Full Logo (`public/images/kasiva-logo-full.png`)
Digunakan pada **Navbar Header Utama**, **Halaman Login**, dan **Struk Digital Kasiva**.

### 3.2 Mark Icon 'K' (`public/images/kasiva-logo-icon.png`)
Digunakan sebagai **Favicon**, **App Icon (Android/iOS/Desktop)**, dan **Avatar Staf Kasir**.

### 3.3 Wordmark (`public/images/kasiva-logo-wordmark.png`)
Digunakan pada **Footer Struk** dan **Dokumentasi Resmi**.
