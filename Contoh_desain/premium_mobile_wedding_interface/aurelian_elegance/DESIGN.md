---
name: Aurelian Elegance
colors:
  surface: '#fbf9f8'
  surface-dim: '#dcd9d9'
  surface-bright: '#fbf9f8'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f6f3f2'
  surface-container: '#f0eded'
  surface-container-high: '#eae7e7'
  surface-container-highest: '#e4e2e1'
  on-surface: '#1b1c1c'
  on-surface-variant: '#4e4538'
  inverse-surface: '#303030'
  inverse-on-surface: '#f3f0f0'
  outline: '#807666'
  outline-variant: '#d2c5b3'
  surface-tint: '#7b580d'
  primary: '#7b580d'
  on-primary: '#ffffff'
  primary-container: '#b68d40'
  on-primary-container: '#3c2900'
  inverse-primary: '#eebf6d'
  secondary: '#605e5a'
  on-secondary: '#ffffff'
  secondary-container: '#e6e2dc'
  on-secondary-container: '#666460'
  tertiary: '#735a31'
  on-tertiary: '#ffffff'
  tertiary-container: '#ac8f61'
  on-tertiary-container: '#3c2904'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdea8'
  primary-fixed-dim: '#eebf6d'
  on-primary-fixed: '#271900'
  on-primary-fixed-variant: '#5e4200'
  secondary-fixed: '#e6e2dc'
  secondary-fixed-dim: '#c9c6c0'
  on-secondary-fixed: '#1c1c18'
  on-secondary-fixed-variant: '#484743'
  tertiary-fixed: '#ffdeac'
  tertiary-fixed-dim: '#e3c290'
  on-tertiary-fixed: '#281900'
  on-tertiary-fixed-variant: '#59431c'
  background: '#fbf9f8'
  on-background: '#1b1c1c'
  surface-variant: '#e4e2e1'
typography:
  display-lg:
    fontFamily: Playfair Display
    fontSize: 48px
    fontWeight: '600'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '500'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 28px
    fontWeight: '500'
    lineHeight: 36px
  headline-md:
    fontFamily: Playfair Display
    fontSize: 24px
    fontWeight: '500'
    lineHeight: 32px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.03em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  margin-page: 24px
  gutter-grid: 16px
  stack-sm: 8px
  stack-md: 16px
  stack-lg: 32px
  stack-xl: 64px
---

## Brand & Style

The design system is centered on the intersection of timeless wedding tradition and contemporary digital minimalism. It targets a high-end audience that values clarity, sophistication, and effortless luxury. The experience must feel intentional, calm, and exclusive.

The design style combines **Minimalism** with **Glassmorphism**. It leverages expansive whitespace (the "luxury of space") and high-quality editorial photography to create an aspirational atmosphere. Drawing from the Apple Human Interface Guidelines, the system prioritizes clarity and tap-friendly targets, while the aesthetic remains warm and inviting through the use of cream tones and soft gold accents. The result is a digital invitation that feels as substantial and curated as a physical high-grammage paper suite.

## Colors

The palette is anchored in a trio of warm metallic and organic tones to evoke a sense of heritage and celebration.

- **Primary (#B68D40):** A deep, refined gold used for key call-to-actions, active states, and decorative serif flourishes.
- **Secondary (#F7F3ED):** A lush cream used for glassmorphic card backgrounds and subtle section separators.
- **Background (#FCFBF8):** A near-white off-white that prevents the "clinical" feel of pure #FFFFFF, providing a soft canvas for imagery.
- **Text (#2F2F2F):** A deep charcoal used instead of black to maintain a softer, high-contrast readability that feels more premium.
- **Accent (#D4B483):** A softer gold used for secondary buttons, borders, and icon details.

## Typography

This design system utilizes a classic serif/sans-serif pairing to balance tradition with modern utility.

**Playfair Display** is used for all editorial headings and names. It should be typeset with generous leading and occasionally negative letter-spacing for large display titles to increase the "fashion-editorial" feel.

**Inter** provides the functional backbone for the system. Its neutral, systematic construction ensures that logistical information (dates, maps, RSVP forms) remains highly legible and professional. Label styles should use uppercase and tracking (letter-spacing) to differentiate them from body text.

## Layout & Spacing

The layout is a mobile-first, single-column fluid flow designed for the 375pt width. 

- **Safe Areas:** Adhere to 24px side margins to ensure content feels framed and never cramped against the screen edges.
- **Vertical Rhythm:** Use a "Stack" philosophy. Smaller components (like a time and location pair) use 8px–16px spacing. Major sections (Story, Gallery, RSVP) are separated by 64px to emphasize the transition.
- **Imagery:** Photos should follow a 4:5 or 9:16 vertical ratio to maximize the mobile screen real estate, often used as full-bleed backgrounds or cards with 24px corner radii.

## Elevation & Depth

Depth is achieved through **Glassmorphism** and **Ambient Shadows** rather than stark borders.

- **Surface Layers:** The primary background is the base. Overlaid cards use a semi-transparent `rgba(247, 243, 237, 0.7)` (Secondary Color) with a 20px backdrop-blur.
- **Shadows:** Use extremely soft, diffuse shadows for floating elements. 
  - *Offset:* 0px 8px, *Blur:* 24px, *Color:* `rgba(47, 47, 47, 0.08)`.
- **Floating Navigation:** The bottom navigation bar should be a floating pill shape, utilizing the glassmorphism effect to let the content scroll subtly underneath it.

## Shapes

The design system employs a consistent **24px (1.5rem)** corner radius for all major containers, imagery, and input fields. This high degree of roundedness communicates softness, approachability, and modern luxury.

Buttons and the floating navigation bar use "Pill" shapes (full-radius) to create a clear interactive distinction from informational cards.

## Components

- **Primary Buttons:** High-end solid fill (#B68D40) with white or cream text. 56px minimum height for tap-ability. Use a subtle inner-glow (top-down) for a tactile feel.
- **Glass Cards:** Used for the RSVP form and Schedule details. They feature a 1px border in `rgba(182, 141, 64, 0.2)` (Primary color at low opacity) to define the edge against the background.
- **Floating Bottom Nav:** A centered pill-shaped bar. Icons are 2pt weight outlines. The active state is indicated by a subtle gold dot underneath the icon.
- **Inputs:** Text fields use a Secondary Color (#F7F3ED) background with 24px corners. Labels are placed above the field in the `label-md` style.
- **Chips/Tags:** Used for guest categories or dietary requirements. These should be thin outlines in Gold (#B68D40) with 12px padding.
- **Image Containers:** Always 24px rounded corners. Use a subtle dark-to-transparent gradient overlay at the bottom if text needs to be placed over the image.