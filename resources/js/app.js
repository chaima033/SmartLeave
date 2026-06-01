import "./bootstrap";

document.addEventListener("submit", async (event) => {
    const form = event.target;

    if (!form.matches("[data-ai-chat-form]")) {
        return;
    }

    event.preventDefault();

    const answerTarget = document.querySelector("[data-ai-answer]");
    const submitButton = form.querySelector("[data-ai-submit]");
    const promptInput = form.querySelector('textarea[name="prompt"]');
    const formData = new FormData(form);

    if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = "Analyse en cours...";
    }

    try {
        const response = await fetch(form.action, {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
            body: formData,
        });

        const payload = await response.json();

        if (!response.ok) {
            throw new Error(
                payload.message || "Impossible de générer la réponse IA.",
            );
        }

        if (answerTarget) {
            answerTarget.innerHTML = payload.answer.replace(/\n/g, "<br>");
        }

        if (promptInput) {
            promptInput.value = "";
        }
    } catch (error) {
        if (answerTarget) {
            answerTarget.textContent = error.message;
        }
    } finally {
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = "Demander à l assistant";
        }
    }
});

const aiForms = document.querySelectorAll("[data-ai-chat]");

aiForms.forEach((form) => {
    const output = form.querySelector("[data-ai-output]");
    const promptField = form.querySelector("[data-ai-prompt]");
    const submitButton = form.querySelector("[data-ai-submit]");
    const template = output?.querySelector("[data-ai-template]");

    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        const endpoint = form.getAttribute("action");
        if (!endpoint || !promptField || !output || !submitButton) {
            return;
        }

        const prompt = promptField.value.trim();
        if (!prompt) {
            return;
        }

        submitButton.disabled = true;
        submitButton.textContent = "Traitement...";

        const userBubble = template ? template.content.cloneNode(true) : null;
        if (userBubble) {
            const userNode = userBubble.querySelector('[data-role="user"]');
            userNode.textContent = prompt;
            output.prepend(userBubble);
        }

        try {
            const response = await fetch(endpoint, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                    Accept: "application/json",
                },
                body: new FormData(form),
            });

            const payload = await response.json();
            const answer = payload.answer || "Aucune reponse.";

            const aiBubble = template ? template.content.cloneNode(true) : null;
            if (aiBubble) {
                const aiNode = aiBubble.querySelector(
                    '[data-role="assistant"]',
                );
                aiNode.textContent = answer;
                output.prepend(aiBubble);
            }

            promptField.value = "";
        } catch (error) {
            const aiBubble = template ? template.content.cloneNode(true) : null;
            if (aiBubble) {
                const aiNode = aiBubble.querySelector(
                    '[data-role="assistant"]',
                );
                aiNode.textContent =
                    "Le service IA est indisponible pour le moment.";
                output.prepend(aiBubble);
            }
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = "Envoyer";
        }
    });
});
