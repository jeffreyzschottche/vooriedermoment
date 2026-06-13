// Eén bron van waarheid voor de prijs. 1 product: één gepersonaliseerd nummer,
// overal beschikbaar (Spotify + Apple Music), 1 gedeelde versie.
// Aanbieding aan/uit via env `saleOn=1` (zie nuxt.config runtimeConfig.public.saleOn).

const REGULAR_PRICE = 14.99;
const SALE_PRICE = 9.99;

function euro(value: number): string {
  return '€ ' + value.toFixed(2).replace('.', ',');
}

export function useOffer() {
  const config = useRuntimeConfig();
  const saleOn = computed(() => Boolean(config.public.saleOn));

  const regularPrice = REGULAR_PRICE;
  const salePrice = SALE_PRICE;
  const currentPrice = computed(() => (saleOn.value ? salePrice : regularPrice));

  return {
    saleOn,
    regularPrice,
    salePrice,
    currentPrice,
    currentPriceCents: computed(() => Math.round(currentPrice.value * 100)),
    formattedRegular: euro(regularPrice),
    formattedSale: euro(salePrice),
    formattedCurrent: computed(() => euro(currentPrice.value)),
  };
}
