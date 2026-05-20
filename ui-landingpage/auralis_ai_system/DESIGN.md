---
name: Auralis AI System
colors:
  surface: '#f9f9f9'
  surface-dim: '#dadada'
  surface-bright: '#f9f9f9'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f3f3'
  surface-container: '#eeeeee'
  surface-container-high: '#e8e8e8'
  surface-container-highest: '#e2e2e2'
  on-surface: '#1a1c1c'
  on-surface-variant: '#4c4546'
  inverse-surface: '#2f3131'
  inverse-on-surface: '#f1f1f1'
  outline: '#7e7576'
  outline-variant: '#cfc4c5'
  surface-tint: '#5e5e5e'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#1b1b1b'
  on-primary-container: '#848484'
  inverse-primary: '#c6c6c6'
  secondary: '#5e5e5e'
  on-secondary: '#ffffff'
  secondary-container: '#e1dfdf'
  on-secondary-container: '#636262'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#07006c'
  on-tertiary-container: '#7073fe'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e2e2e2'
  primary-fixed-dim: '#c6c6c6'
  on-primary-fixed: '#1b1b1b'
  on-primary-fixed-variant: '#474747'
  secondary-fixed: '#e4e2e2'
  secondary-fixed-dim: '#c7c6c6'
  on-secondary-fixed: '#1b1c1c'
  on-secondary-fixed-variant: '#464747'
  tertiary-fixed: '#e1e0ff'
  tertiary-fixed-dim: '#c0c1ff'
  on-tertiary-fixed: '#07006c'
  on-tertiary-fixed-variant: '#2f2ebe'
  background: '#f9f9f9'
  on-background: '#1a1c1c'
  surface-variant: '#e2e2e2'
  editor-bg: '#111318'
  surface-lowest: '#FFFFFF'
  surface-low: '#F3F3F3'
  outline-muted: '#E2E2E2'
  wave-active: '#000000'
typography:
  display-lg:
    fontFamily: Geist
    fontSize: 84px
    fontWeight: '600'
    lineHeight: '0.95'
    letterSpacing: -0.04em
  display-lg-mobile:
    fontFamily: Geist
    fontSize: 48px
    fontWeight: '600'
    lineHeight: '1.1'
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Geist
    fontSize: 48px
    fontWeight: '600'
    lineHeight: '1.1'
    letterSpacing: -0.02em
  title-lg:
    fontFamily: Geist
    fontSize: 28px
    fontWeight: '500'
    lineHeight: '1.2'
    letterSpacing: -0.01em
  body-md:
    fontFamily: Geist
    fontSize: 17px
    fontWeight: '400'
    lineHeight: '1.6'
    letterSpacing: 0em
  button:
    fontFamily: Geist
    fontSize: 14px
    fontWeight: '500'
    lineHeight: '1.0'
    letterSpacing: 0.01em
  label-caps:
    fontFamily: Geist
    fontSize: 12px
    fontWeight: '600'
    lineHeight: '1.0'
    letterSpacing: 0.05em
  code:
    fontFamily: jetbrainsMono
    fontSize: 14px
    fontWeight: '400'
    lineHeight: '1.5'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  container-max: 1280px
  gutter: 24px
  section-gap: 150px
  internal-sm: 12px
  internal-md: 24px
  internal-lg: 32px
---

## Brand & Style

Auralis is defined by a **Technical Minimalism** aesthetic, blending high-end corporate reliability with developer-centric precision. The visual language evokes a sense of "Engineered Humanism"—AI that is sophisticated and powerful, yet accessible and clean.

The design style leverages **Modern Minimalism** with subtle **Glassmorphic** accents. It prioritizes extreme clarity, generous white space, and a restricted, high-contrast palette to ensure that technical data (like audio waveforms and code snippets) feels premium rather than cluttered. The emotional response is one of trust, efficiency, and cutting-edge performance.

## Colors

The palette is anchored in a monochromatic core to maintain a professional, utility-first feel. 

*   **Primary Black (#000000)** is used for high-intent actions, typography, and structural emphasis.
*   **Neutral Greys** form the foundation of the interface, using a sophisticated "Surface Container" logic where depth is communicated through slight shifts in light grey hex values rather than shadows.
*   **Tertiary Blue (#2F2EBE)** acts as a precise technical accent, reserved for "New" badges, specific syntax highlighting in code, and active states in data visualizations.
*   **Dark Mode Override:** The code editor and specific "pro" cards utilize a deep obsidian (#111318) to provide a high-contrast break from the otherwise light, airy interface.

## Typography

The system utilizes **Geist** across all primary roles to reinforce the technical and developer-focused brand identity. Geist's geometric but highly legible structure is perfect for both massive marketing headlines and dense interface labels.

*   **Headlines:** Feature tight line-heights and negative letter-spacing to create a "locked-in," professional editorial look.
*   **Body:** Uses a comfortable 1.6 line-height for long-form explanatory text.
*   **Mono Space:** **JetBrains Mono** is introduced for API blocks and technical data points, ensuring character distinction in code snippets.
*   **Responsive Scaling:** The display font shrinks significantly on mobile (from 84px to 48px) to maintain the "compact" visual density characteristic of the brand.

## Layout & Spacing

Auralis uses a **Fixed Grid** philosophy for its marketing containers (1280px max-width) while employing a fluid, flex-based approach for internal dashboard components. 

The rhythm is defined by a massive vertical section gap (150px) that enforces the "Minimalist" brand value through intentional negative space. Gutters are consistently set at 24px. Layouts favor a 12-column grid on desktop, collapsing to a single column on mobile, with cards typically spanning 4 columns (3-up) or 6 columns (2-up) depending on the content hierarchy.

## Elevation & Depth

Depth is primarily achieved through **Tonal Layering** and **Low-Contrast Outlines** rather than traditional drop shadows.

*   **Surfaces:** The background uses `surface-bright`. Interactive cards use `surface-container-lowest` with a 1px border of `outline-variant`.
*   **Glassmorphism:** The top navigation bar and floating editor panels use a semi-transparent background (80% opacity) with a high `backdrop-blur` (24px) to suggest a sense of light, layered complexity.
*   **Shadows:** When shadows are used (e.g., the Pro pricing card), they are extremely diffused and subtle (`shadow-md`), intended to guide the eye rather than create physical realism.

## Shapes

The shape language is a mix of **Pill-shaped** interactive elements and **Soft-Rounded** containers.

*   **Buttons & Badges:** Always use `rounded-full` (9999px) to contrast against the more rigid grid structure, making them feel approachable and tactile.
*   **Cards & Modules:** Use a larger radius (24px or `rounded-3xl`) to create a soft, modern frame for technical content.
*   **Inputs & Small Modules:** Standardized to a 12px radius (`rounded-xl`) to maintain consistency with the card interior elements.

## Components

### Buttons
*   **Primary:** Solid black background, white text, pill-shaped, with a subtle scale-down effect (active:scale-95) for tactile feedback.
*   **Secondary/Ghost:** Transparent background with a 1px outline of `outline-variant`. Pill-shaped.

### Cards
*   **Standard:** `surface-container-lowest` background, 1px `outline-variant` border, 24px padding.
*   **Feature Grid:** Includes a `surface-container` header area for icons, creating a distinct visual break within the card.

### Technical Elements
*   **Waveforms:** Represented as vertical bars of varying heights. Active bars use `primary`, while inactive/background bars use `outline`.
*   **Code Blocks:** Dark themed (#111318) even in light mode, with monospaced typography and color-coded syntax.

### Input Fields
*   **Standard Input:** `surface-container` background with a 1px border. Focus states use a 1px `primary` ring.
*   **Transcript Editor:** Uses high-contrast spans (e.g., `primary/10` background with `primary` text) to highlight specific edited syllables or tokens.