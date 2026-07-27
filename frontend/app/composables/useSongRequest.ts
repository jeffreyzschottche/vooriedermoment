// Intake + checkout-flow tegen de Laravel backend. Houdt de lopende aanvraag
// vast in een Pinia-achtige client state (useState) zodat /checkout en /bedankt
// de gegevens kennen. Betaling is voorlopig gestubd (backend StubPaymentProvider).

import { useApi } from '~/composables/useApi';
import type { AccentTheme } from '~/data/categories';

export interface SongRequestPayload {
  category: string;
  categoryTitle: string;
  intake: Record<string, string | string[]>;
  // Alleen client-side: kleur van de gekozen categorie, zodat checkout en
  // bedankt-pagina dezelfde huisstijl aanhouden. Wordt niet naar de backend gestuurd.
  theme?: AccentTheme | null;
}

export interface SongRequestResult {
  id: number;
  category: string;
  status: string;
  price_cents: number;
  lyrics_preview?: string | null;
  production_steps?: Record<string, unknown> | null;
  music_reference?: string | null;
  checkout_url?: string | null;
  discount_applied?: boolean;
}

export function useSongRequest() {
  const api = useApi();
  const current = useState<SongRequestResult | null>('songRequest', () => null);
  const lastPayload = useState<SongRequestPayload | null>('songRequestPayload', () => null);
  // Kleur van de gekozen categorie; checkout/bedankt kleuren hiermee mee.
  const theme = useState<AccentTheme | null>('songRequestTheme', () => null);

  async function create(payload: SongRequestPayload): Promise<SongRequestResult> {
    const { theme: payloadTheme, ...apiPayload } = payload;
    theme.value = payloadTheme ?? null;
    lastPayload.value = payload;
    current.value = null;

    const res = await api.post<{ data: SongRequestResult }>('/song-requests', apiPayload);
    current.value = res.data ?? (res as any);

    return current.value;
  }

  async function checkout(discountCode = ''): Promise<SongRequestResult | null> {
    // Herstel oude browsersessies waarin de eerdere demo-fallback id 0 opsloeg.
    if ((!current.value || current.value.id <= 0) && lastPayload.value) {
      await create(lastPayload.value);
    }

    if (!current.value || current.value.id <= 0) {
      throw new Error('De aanvraag kon niet met de backend worden gekoppeld.');
    }

    const res = await api.post<{ data: SongRequestResult }>(
      `/song-requests/${current.value.id}/checkout`,
      discountCode.trim() ? { discount_code: discountCode.trim() } : {},
    );
    current.value = res.data ?? (res as any);

    return current.value;
  }

  return { current, lastPayload, theme, create, checkout };
}
