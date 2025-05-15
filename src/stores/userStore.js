import { writable } from "svelte/store"

export const userStore = writable({
  isAuthenticated: false,
  user: null,
  token: null,
})
