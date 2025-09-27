import { ref, computed, onMounted, onBeforeUnmount, watch } from "vue";

export function useCountdown(endDate) {
  const now = ref(Date.now());
  let timer = null;

  const end = computed(() => {
    const v = typeof endDate === "function" ? endDate() : endDate;
    return new Date(v).getTime();
  });

  const diff = computed(() => Math.max(0, end.value - now.value));
  const days = computed(() => Math.floor(diff.value / (1000 * 60 * 60 * 24)));
  const hours = computed(() =>
    Math.floor((diff.value / (1000 * 60 * 60)) % 24)
  );
  const minutes = computed(() => Math.floor((diff.value / (1000 * 60)) % 60));
  const seconds = computed(() => Math.floor((diff.value / 1000) % 60));
  const isOver = computed(() => diff.value <= 0);

  const tick = () => {
    now.value = Date.now();
  };

  onMounted(() => {
    tick();
    timer = setInterval(tick, 1000);
  });
  onBeforeUnmount(() => {
    if (timer) clearInterval(timer);
  });

  watch(end, tick);

  return { days, hours, minutes, seconds, isOver };
}
