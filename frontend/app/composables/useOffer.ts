// Eén bron van waarheid voor de prijs: één persoonlijk nummer en één gedeelde
// versie voor Spotify en Apple Music. Kortingsweergave blijft voorbereid voor
// een toekomstige aanbieding.

const REGULAR_PRICE = 9.99;
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
  // Alleen een echte korting tonen als de reguliere prijs hoger is dan de actieprijs.
  const hasDiscount = computed(() => saleOn.value && regularPrice > salePrice);

  return {
    saleOn,
    hasDiscount,
    regularPrice,
    salePrice,
    currentPrice,
    currentPriceCents: computed(() => Math.round(currentPrice.value * 100)),
    formattedRegular: euro(regularPrice),
    formattedSale: euro(salePrice),
    formattedCurrent: computed(() => euro(currentPrice.value)),
  };
}
