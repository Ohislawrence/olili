import { ref } from "vue"

export default function usePWA() {
    const deferredPrompt = ref(null)
    const canInstall = ref(false)

    window.addEventListener("beforeinstallprompt", (e) => {
        e.preventDefault()
        deferredPrompt.value = e
        canInstall.value = true
    })

    const install = async () => {
        if (!deferredPrompt.value) return

        deferredPrompt.value.prompt()
        const choice = await deferredPrompt.value.userChoice

        if (choice.outcome === "accepted") {
            console.log("PWA installed")
        }

        deferredPrompt.value = null
        canInstall.value = false
    }

    return { canInstall, install }
}
