import { writable } from "svelte/store"

export const cartStore = writable({
  items: [],
  total: 0,
  itemCount: 0,
})
