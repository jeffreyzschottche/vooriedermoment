# Design System Strategy: Personalized Luxury Audio

## 1. Overview & Creative North Star
**The Creative North Star: "The Digital Concierge"**

This design system is not a utility; it is an experience. For a personalized music service like 'Voor Ieder Moment', the interface must mirror the emotional resonance of a live private performance. We move away from the "SaaS dashboard" aesthetic toward a **High-End Editorial** approach. 

The system breaks traditional digital templates through:
*   **Intentional Asymmetry:** Overlapping typography and imagery to create movement.
*   **Tonal Depth:** Replacing harsh lines with sophisticated layering.
*   **Emotional Scale:** Using dramatic shifts in type size to guide the user's journey through Dutch sentiments and musical storytelling.

---

## 2. Colors & Surface Philosophy

The palette is rooted in the interplay between deep matte tones and the warmth of the extracted Gold.

### The "No-Line" Rule
**Strict Mandate:** Designers are prohibited from using 1px solid borders for sectioning or containment. Structural boundaries must be achieved through:
*   **Background Shifts:** Transitioning from `surface` (#131313) to `surface_container_low` (#1C1B1B).
*   **Tonal Nesting:** A `surface_container_highest` (#353534) element sitting on a `surface_container` (#201F1F) creates a natural edge without a line.

### Glass & Gradient Signature
To avoid a flat, "cheap" feel, use **Glassmorphism** for floating audio players or navigation overlays.
*   **Token:** `surface_variant` (#353534) at 60% opacity with a 20px Backdrop Blur.
*   **Gradients:** Main CTAs should use a subtle linear gradient from `primary` (#F2CA50) to `primary_container` (#D4AF37) at a 135-degree angle to simulate the shimmer of real gold leaf.

---

## 3. Typography

Typography is the "voice" of the service. We use a pairing of a timeless Serif and a modern, high-legibility Sans.

*   **Display & Headlines (Noto Serif):** Used for emotional hooks and page titles. The Serif conveys heritage, professionalism, and the "moment" (e.g., *“Een uniek lied voor een bijzonder mens”*).
*   **Titles & Body (Manrope):** A clean, geometric sans-serif that ensures clarity for service details, pricing, and Dutch functional text.
*   **The Scale:** 
    *   `display-lg` (3.5rem) is reserved for hero emotional statements.
    *   `label-sm` (0.6875rem) is used for metadata like "TIJDSDUUR" or "GENRE", set in All-Caps with 0.1rem letter spacing for an archival look.

---

## 4. Elevation & Depth

We eschew "Material" shadows for **Ambient Tonal Layering**.

*   **The Layering Principle:** Stacking follows a physical logic. The `surface_container_lowest` (#0E0E0E) is the foundation. Important "interactive" cards use `surface_container_high` (#2A2A2A) to naturally pull forward.
*   **Ambient Shadows:** If a card must float (e.g., a modal), use a shadow with a blur of 40px and 6% opacity. The shadow color should be a tinted version of the background, never pure black.
*   **The Ghost Border:** For accessibility in input fields, use `outline_variant` (#4D4635) at **15% opacity**. It should feel felt, not seen.

---

## 5. Components

### Buttons
*   **Primary:** Gradient Gold (`primary` to `primary_container`). Text in `on_primary` (#3C2F00). Roundedness: `md` (0.375rem) for a modern yet stable feel.
*   **Secondary:** Ghost style. No background, `outline` border at 20% opacity. Text in `primary`.
*   **Tertiary:** Text-only in `secondary`, underlined only on hover.

### Cards & Music Lists
*   **Rule:** No dividers (`<hr>`). Separate songs or services using `1.5` (0.5rem) spacing or a subtle shift to `surface_container_low`.
*   **Interaction:** On hover, a card should shift from `surface_container` to `surface_container_high` and slightly scale (1.02x) for a "tactile" response.

### Input Fields
*   **Style:** Underline-only or subtle `surface_container_lowest` backgrounds. 
*   **Error State:** Use `error` (#FFB4AB) only for the helper text; the field border should remain subtle to maintain the luxury feel.

### Unique Component: The Audio "Moment" Player
A floating bar using the **Glassmorphism** rule. The progress bar should be a 2px thin line of `primary_fixed_dim` (#E9C349) with a glowing `primary` thumb.

---

## 6. Do's and Don'ts

### Do
*   **Do** use Dutch with intention. Luxury is often found in brevity (*"Jouw verhaal, onze muziek"*).
*   **Do** use the Spacing Scale (specifically `10`, `12`, and `16`) to create massive "white space" (black space) around key elements.
*   **Do** ensure the Gold is used sparingly. It is a "highlight," not a "paint."

### Don't
*   **Don't** use pure white (#FFFFFF) for body text. Use `on_surface_variant` (#D0C5AF) to reduce eye strain and feel more "matte."
*   **Don't** use `full` (9999px) pill-shaped buttons for everything. Stick to `md` (0.375rem) to maintain a professional, architectural structure.
*   **Don't** use standard "drop shadows" that look like offset black circles. All depth must feel like light hitting a surface.

---

## 7. Dutch Language Localization
*   **Tone:** Formeel (U-vorm) or Warm-Informeel (Je-vorm) depending on the moment, but always respectful.
*   **Hyphenation:** Dutch has long compound words (e.g., *Herinneringsmuziek*). Ensure `display` type has proper CSS `hyphens: auto` and enough horizontal breathing room.