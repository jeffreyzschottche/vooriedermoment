// Echte klantreviews voor de homepage. Alleen reviews toevoegen die een klant
// echt heeft gegeven (met toestemming om te tonen): neppe reviews zijn
// misleidend voor bezoekers, verboden onder de consumentenregels (ACM) en
// Google straft nep-review-markup af. Zolang de lijst leeg is, verbergt de
// homepage de reviewsectie en de beoordeling in de structured data.

export interface Review {
  name: string; // bv. "Sanne" of "Sanne uit Utrecht"
  moment: string; // bv. "Verjaardag"
  rating: 1 | 2 | 3 | 4 | 5;
  text: string;
  date: string; // ISO, bv. "2026-10-12"
}

export const reviews: Review[] = [];
