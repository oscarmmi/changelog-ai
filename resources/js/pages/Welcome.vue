<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type DemoExample = {
    id: number;
    title: string;
    raw_text: string;
};

type ChangelogResult = {
    job_id: number;
    breaking: string[];
    features: string[];
    fixes: string[];
    rewritten_markdown: string | null;
};

const props = defineProps<{
    examples: DemoExample[];
    result: ChangelogResult | null;
    rewriteError: string | null;
    llmMode: 'Gemini' | 'Local preview';
}>();

const form = useForm({ raw_text: '', rewrite: true });
const copied = ref(false);
const totalChanges = computed(
    () =>
        (props.result?.breaking.length ?? 0) +
        (props.result?.features.length ?? 0) +
        (props.result?.fixes.length ?? 0),
);

function loadExample(example: DemoExample) {
    form.raw_text = example.raw_text;
    form.clearErrors();
}

function analyze() {
    copied.value = false;
    form.post('/', { preserveScroll: true });
}

async function copyReleaseNotes() {
    if (!props.result) return;

    const markdown =
        props.result.rewritten_markdown ??
        [
            ['Breaking changes', props.result.breaking],
            ['Features', props.result.features],
            ['Fixes', props.result.fixes],
        ]
            .filter(([, items]) => (items as string[]).length > 0)
            .map(
                ([heading, items]) =>
                    `## ${heading}\n${(items as string[]).map((item) => `- ${item}`).join('\n')}`,
            )
            .join('\n\n');

    await navigator.clipboard.writeText(markdown);
    copied.value = true;
    window.setTimeout(() => (copied.value = false), 1800);
}
</script>

<template>
    <Head title="Changelog AI">
        <meta
            name="description"
            content="Turn a Keep a Changelog file into clear, grouped release notes."
        />
    </Head>

    <div class="min-h-screen bg-[#f7f7f4] text-[#1e2927]">
        <header class="border-b border-[#e7e8e2] bg-white/85">
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-8"
            >
                <a
                    href="/"
                    class="flex items-center gap-3"
                    aria-label="Changelog AI home"
                >
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#173f38] text-[#e5c781] shadow-sm"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            class="h-5 w-5"
                            aria-hidden="true"
                        >
                            <path
                                d="M7 4.75h7l4 4v10.5H7a2 2 0 0 1-2-2v-10.5a2 2 0 0 1 2-2Z"
                            />
                            <path d="M14 4.75v4h4M8.5 12h7M8.5 15.5h7" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm font-semibold tracking-tight"
                            >Changelog AI</span
                        >
                        <span
                            class="block text-[11px] tracking-wide text-[#7b8580]"
                            >RELEASE NOTE STUDIO</span
                        >
                    </span>
                </a>

                <div
                    class="hidden items-center gap-2 rounded-full border border-[#e5e7df] px-3 py-1.5 text-xs text-[#65736d] sm:flex"
                >
                    <span class="h-1.5 w-1.5 rounded-full bg-[#63a783]" />
                    Deterministic parsing · optional AI
                </div>

                <a
                    href="https://github.com/oscarmmi/changelog-ai"
                    target="_blank"
                    rel="noreferrer"
                    class="inline-flex items-center gap-2 text-sm font-medium text-[#52605b] transition hover:text-[#173f38]"
                >
                    View source
                    <svg
                        viewBox="0 0 20 20"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        class="h-4 w-4"
                        aria-hidden="true"
                    >
                        <path d="M7 4h9v9M15.5 4.5 6 14" />
                    </svg>
                </a>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-5 pt-12 pb-16 sm:px-8 sm:pt-16">
            <section class="mb-10 max-w-3xl">
                <div
                    class="mb-5 inline-flex items-center gap-2 rounded-full bg-[#e8eee9] px-3 py-1.5 text-xs font-medium text-[#315a4f]"
                >
                    <span class="font-mono text-[11px]">MD</span>
                    From changelog to customer-ready
                </div>
                <h1
                    class="text-4xl font-semibold tracking-[-0.045em] text-[#1c2d29] sm:text-5xl"
                >
                    Every release has a story.
                    <span class="block text-[#688078]"
                        >Make yours easy to read.</span
                    >
                </h1>
                <p
                    class="mt-5 max-w-2xl text-base leading-7 text-[#68756f] sm:text-lg"
                >
                    Paste a Keep a Changelog file. Get the important changes
                    grouped into breaking changes, features, and fixes—with an
                    optional AI polish on top.
                </p>
            </section>

            <div class="grid items-start gap-6 lg:grid-cols-[1.02fr_0.98fr]">
                <section
                    class="overflow-hidden rounded-2xl border border-[#e3e6df] bg-white shadow-[0_12px_36px_-30px_rgba(27,52,44,0.35)]"
                >
                    <div
                        class="flex items-center justify-between border-b border-[#edf0eb] px-5 py-4 sm:px-6"
                    >
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#f0f3ef] text-[#587368]"
                            >
                                <svg
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    class="h-4 w-4"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M5.5 3.5h9v13h-9zM8 7h4M8 10h4M8 13h2.5"
                                    />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-sm font-semibold">
                                    Your changelog
                                </h2>
                                <p class="mt-0.5 text-xs text-[#89938d]">
                                    Markdown · up to 50,000 characters
                                </p>
                            </div>
                        </div>
                        <span class="font-mono text-[11px] text-[#96a09a]"
                            >INPUT.md</span
                        >
                    </div>

                    <form class="p-5 sm:p-6" @submit.prevent="analyze">
                        <label for="changelog" class="sr-only"
                            >Paste your changelog</label
                        >
                        <textarea
                            id="changelog"
                            v-model="form.raw_text"
                            name="raw_text"
                            rows="15"
                            maxlength="50000"
                            spellcheck="false"
                            placeholder="# Product name&#10;&#10;## [1.2.0] - 2026-10-01&#10;&#10;### Added&#10;- Add a searchable activity feed.&#10;&#10;### Fixed&#10;- Resolve an issue with saved filters."
                            class="w-full resize-y rounded-xl border border-[#e4e8e2] bg-[#fbfcfa] px-4 py-4 font-mono text-[13px] leading-6 text-[#33433d] transition outline-none placeholder:text-[#adb5af] focus:border-[#6d9786] focus:bg-white focus:ring-4 focus:ring-[#eaf1ed]"
                            :aria-invalid="Boolean(form.errors.raw_text)"
                            aria-describedby="changelog-hint changelog-error"
                        />
                        <div class="mt-2 flex justify-between gap-3 text-xs">
                            <p id="changelog-hint" class="text-[#929c95]">
                                We look for Breaking, Added, Changed, and Fixed
                                sections.
                            </p>
                            <span class="shrink-0 font-mono text-[#929c95]"
                                >{{ form.raw_text.length.toLocaleString() }} /
                                50k</span
                            >
                        </div>
                        <p
                            v-if="form.errors.raw_text"
                            id="changelog-error"
                            class="mt-2 text-sm text-[#b64a3f]"
                            role="alert"
                        >
                            {{ form.errors.raw_text }}
                        </p>

                        <div class="my-5 h-px bg-[#edf0eb]" />

                        <label
                            class="flex cursor-pointer items-start gap-3 rounded-xl border border-[#e6ebe6] bg-[#fafbf9] p-4 transition hover:border-[#d0ddd4]"
                        >
                            <input
                                v-model="form.rewrite"
                                type="checkbox"
                                name="rewrite"
                                class="mt-0.5 h-4 w-4 rounded border-[#c8d1ca] accent-[#275b4d] focus:ring-[#275b4d]"
                            />
                            <span class="min-w-0 flex-1">
                                <span
                                    class="flex flex-wrap items-center gap-2 text-sm font-medium text-[#33433d]"
                                >
                                    Polish the grouped notes
                                    <span
                                        class="rounded-full bg-[#f4eedc] px-2 py-0.5 text-[10px] font-semibold tracking-wide text-[#8c7136] uppercase"
                                    >
                                        {{ llmMode }}
                                    </span>
                                </span>
                                <span
                                    class="mt-1 block text-xs leading-5 text-[#85918a]"
                                >
                                    Use the parsed groups as context for a
                                    concise Markdown rewrite.
                                </span>
                            </span>
                            <span
                                class="hidden rounded-md border border-[#e2e6df] bg-white px-2 py-1 font-mono text-[10px] text-[#8a958e] sm:block"
                            >
                                12s max
                            </span>
                        </label>

                        <button
                            type="submit"
                            :disabled="form.processing || !form.raw_text.trim()"
                            class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-[#1c493f] px-5 py-3.5 text-sm font-semibold text-white shadow-[0_4px_12px_-5px_rgba(28,73,63,0.8)] transition hover:bg-[#153b33] focus:ring-4 focus:ring-[#dce9e2] focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <svg
                                v-if="form.processing"
                                class="h-4 w-4 animate-spin"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="9"
                                    stroke="currentColor"
                                    stroke-opacity=".3"
                                    stroke-width="3"
                                />
                                <path
                                    d="M21 12a9 9 0 0 0-9-9"
                                    stroke="currentColor"
                                    stroke-width="3"
                                />
                            </svg>
                            <svg
                                v-else
                                viewBox="0 0 20 20"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                                class="h-4 w-4"
                                aria-hidden="true"
                            >
                                <path d="M10 2.5v15M2.5 10h15" />
                            </svg>
                            {{
                                form.processing
                                    ? 'Organizing your release notes…'
                                    : 'Generate release notes'
                            }}
                        </button>
                    </form>
                </section>

                <section class="space-y-5">
                    <div class="flex items-center justify-between px-1">
                        <div>
                            <p
                                class="text-[11px] font-semibold tracking-[0.16em] text-[#8a958e] uppercase"
                            >
                                Try a sample
                            </p>
                            <h2
                                class="mt-1 text-lg font-semibold tracking-tight"
                            >
                                Harbor Desk release history
                            </h2>
                        </div>
                        <span
                            class="rounded-full border border-[#e2e6df] bg-white px-2.5 py-1 text-[11px] text-[#829087]"
                            >Fictional product</span
                        >
                    </div>

                    <div
                        class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2"
                    >
                        <button
                            v-for="example in examples"
                            :key="example.id"
                            type="button"
                            class="group flex min-h-[124px] flex-col justify-between rounded-2xl border border-[#e3e6df] bg-white p-4 text-left shadow-[0_8px_28px_-28px_rgba(27,52,44,0.35)] transition hover:-translate-y-0.5 hover:border-[#b9cabe] hover:shadow-[0_14px_34px_-28px_rgba(27,52,44,0.5)] focus:ring-4 focus:ring-[#e4eee8] focus:outline-none"
                            @click="loadExample(example)"
                        >
                            <span
                                class="flex items-center justify-between gap-3"
                            >
                                <span
                                    class="text-sm font-semibold text-[#35463f]"
                                    >{{ example.title }}</span
                                >
                                <span
                                    v-if="
                                        example.raw_text
                                            .toLowerCase()
                                            .includes('breaking')
                                    "
                                    class="rounded-full bg-[#f6e9e5] px-2 py-1 text-[10px] font-semibold text-[#a85b4d]"
                                >
                                    BREAKING CHANGE
                                </span>
                                <span
                                    v-else
                                    class="rounded-full bg-[#edf2ed] px-2 py-1 text-[10px] font-semibold text-[#64806e]"
                                >
                                    PATCH RELEASE
                                </span>
                            </span>
                            <span
                                class="mt-4 flex items-center justify-between text-xs text-[#8b968f]"
                            >
                                <span
                                    >{{
                                        (example.raw_text.match(/^- /gm) ?? [])
                                            .length
                                    }}
                                    changelog items</span
                                >
                                <span
                                    class="inline-flex items-center gap-1 font-medium text-[#4c7665] group-hover:text-[#1c493f]"
                                >
                                    Load sample
                                    <svg
                                        viewBox="0 0 16 16"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        class="h-3.5 w-3.5"
                                        aria-hidden="true"
                                    >
                                        <path d="M3 8h10M9 4l4 4-4 4" />
                                    </svg>
                                </span>
                            </span>
                        </button>
                    </div>

                    <div
                        class="rounded-2xl border border-[#e5e8e2] bg-[#f0f2ed] p-5 sm:p-6"
                    >
                        <div
                            class="flex items-center gap-2 text-xs font-semibold tracking-[0.13em] text-[#73827a] uppercase"
                        >
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-[#547665]"
                                >01</span
                            >
                            How it works
                        </div>
                        <div
                            class="mt-5 grid gap-4 sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3"
                        >
                            <div>
                                <p class="font-mono text-xs text-[#8d9b91]">
                                    INPUT
                                </p>
                                <p class="mt-1 text-sm font-medium">
                                    Paste Markdown
                                </p>
                            </div>
                            <div
                                class="border-[#dfe4dd] sm:border-l sm:pl-4 lg:border-l-0 lg:pl-0 xl:border-l xl:pl-4"
                            >
                                <p class="font-mono text-xs text-[#8d9b91]">
                                    PARSE
                                </p>
                                <p class="mt-1 text-sm font-medium">
                                    Group by meaning
                                </p>
                            </div>
                            <div
                                class="border-[#dfe4dd] sm:border-l sm:pl-4 lg:border-l-0 lg:pl-0 xl:border-l xl:pl-4"
                            >
                                <p class="font-mono text-xs text-[#8d9b91]">
                                    OPTIONAL
                                </p>
                                <p class="mt-1 text-sm font-medium">
                                    Rewrite those groups
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <section
                v-if="result"
                class="mt-8 scroll-mt-8 rounded-2xl border border-[#dfe5df] bg-white p-5 shadow-[0_16px_40px_-32px_rgba(27,52,44,0.5)] sm:p-7"
                aria-live="polite"
            >
                <div
                    class="flex flex-wrap items-start justify-between gap-4 border-b border-[#edf0eb] pb-5"
                >
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="rounded-full bg-[#eaf1ec] px-2.5 py-1 text-[10px] font-semibold tracking-wider text-[#4e7763] uppercase"
                                >Parse complete</span
                            >
                            <span class="font-mono text-xs text-[#99a29b]"
                                >JOB #{{ result.job_id }}</span
                            >
                        </div>
                        <h2 class="mt-3 text-2xl font-semibold tracking-tight">
                            Release notes at a glance
                        </h2>
                        <p class="mt-1 text-sm text-[#849087]">
                            {{ totalChanges }}
                            {{ totalChanges === 1 ? 'change' : 'changes' }}
                            found across your changelog.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-[#dfe5df] px-3 py-2 text-sm font-medium text-[#4d6358] transition hover:border-[#b9cabe] hover:bg-[#f8faf8]"
                        @click="copyReleaseNotes"
                    >
                        <svg
                            v-if="!copied"
                            viewBox="0 0 20 20"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            class="h-4 w-4"
                            aria-hidden="true"
                        >
                            <rect x="7" y="6" width="9" height="11" rx="1.5" />
                            <path
                                d="M13 6V4.5A1.5 1.5 0 0 0 11.5 3h-7A1.5 1.5 0 0 0 3 4.5v9A1.5 1.5 0 0 0 4.5 15H7"
                            />
                        </svg>
                        <svg
                            v-else
                            viewBox="0 0 20 20"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            class="h-4 w-4 text-[#3f805f]"
                            aria-hidden="true"
                        >
                            <path d="m4 10 4 4 8-8" />
                        </svg>
                        {{ copied ? 'Copied' : 'Copy Markdown' }}
                    </button>
                </div>

                <p
                    v-if="rewriteError"
                    class="mt-5 rounded-xl border border-[#f1dfc4] bg-[#fffaf1] px-4 py-3 text-sm text-[#896b3d]"
                    role="status"
                >
                    {{ rewriteError }}
                </p>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <article
                        class="rounded-xl border border-[#f0deda] bg-[#fffaf8] p-4"
                    >
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-[#c87665]" />
                            <h3 class="text-sm font-semibold text-[#744d46]">
                                Breaking changes
                            </h3>
                            <span
                                class="ml-auto font-mono text-xs text-[#b38b81]"
                                >{{ result.breaking.length }}</span
                            >
                        </div>
                        <ul
                            v-if="result.breaking.length"
                            class="mt-4 space-y-3"
                        >
                            <li
                                v-for="(item, index) in result.breaking"
                                :key="`breaking-${index}`"
                                class="flex gap-2.5 text-sm leading-6 text-[#654f4a]"
                            >
                                <span
                                    class="mt-[9px] h-1 w-1 shrink-0 rounded-full bg-[#c87665]"
                                />
                                <span>{{ item }}</span>
                            </li>
                        </ul>
                        <p v-else class="mt-4 text-sm text-[#aa928b]">
                            No breaking changes detected.
                        </p>
                    </article>

                    <article
                        class="rounded-xl border border-[#dfe9e1] bg-[#fbfdfa] p-4"
                    >
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-[#5d9a73]" />
                            <h3 class="text-sm font-semibold text-[#41634d]">
                                Features
                            </h3>
                            <span
                                class="ml-auto font-mono text-xs text-[#88a18e]"
                                >{{ result.features.length }}</span
                            >
                        </div>
                        <ul
                            v-if="result.features.length"
                            class="mt-4 space-y-3"
                        >
                            <li
                                v-for="(item, index) in result.features"
                                :key="`feature-${index}`"
                                class="flex gap-2.5 text-sm leading-6 text-[#506457]"
                            >
                                <span
                                    class="mt-[9px] h-1 w-1 shrink-0 rounded-full bg-[#5d9a73]"
                                />
                                <span>{{ item }}</span>
                            </li>
                        </ul>
                        <p v-else class="mt-4 text-sm text-[#8fa093]">
                            No features detected.
                        </p>
                    </article>

                    <article
                        class="rounded-xl border border-[#e1e5ee] bg-[#fbfbfe] p-4"
                    >
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-[#7586b7]" />
                            <h3 class="text-sm font-semibold text-[#4e5c83]">
                                Fixes
                            </h3>
                            <span
                                class="ml-auto font-mono text-xs text-[#8b98bc]"
                                >{{ result.fixes.length }}</span
                            >
                        </div>
                        <ul v-if="result.fixes.length" class="mt-4 space-y-3">
                            <li
                                v-for="(item, index) in result.fixes"
                                :key="`fix-${index}`"
                                class="flex gap-2.5 text-sm leading-6 text-[#515b73]"
                            >
                                <span
                                    class="mt-[9px] h-1 w-1 shrink-0 rounded-full bg-[#7586b7]"
                                />
                                <span>{{ item }}</span>
                            </li>
                        </ul>
                        <p v-else class="mt-4 text-sm text-[#929bb3]">
                            No fixes detected.
                        </p>
                    </article>
                </div>

                <div
                    v-if="result.rewritten_markdown"
                    class="mt-5 overflow-hidden rounded-xl border border-[#e4e7df]"
                >
                    <div
                        class="flex items-center justify-between border-b border-[#ecefe9] bg-[#fafbf9] px-4 py-3"
                    >
                        <div class="flex items-center gap-2">
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-md bg-[#f2ecd9] text-[#91783e]"
                            >
                                <svg
                                    viewBox="0 0 16 16"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.4"
                                    class="h-3.5 w-3.5"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="m8 1.5 1.5 4 4 1.5-4 1.5-1.5 4-1.5-4-4-1.5 4-1.5L8 1.5Z"
                                    />
                                </svg>
                            </span>
                            <h3 class="text-sm font-semibold">
                                Polished Markdown
                            </h3>
                        </div>
                        <span
                            class="font-mono text-[10px] tracking-wider text-[#9ba39c] uppercase"
                            >{{ llmMode }}</span
                        >
                    </div>
                    <pre
                        class="overflow-x-auto p-5 font-mono text-[13px] leading-6 whitespace-pre-wrap text-[#44534b]"
                        >{{ result.rewritten_markdown }}</pre>
                </div>
            </section>

            <footer
                class="mt-12 flex flex-col justify-between gap-3 border-t border-[#e4e7e1] pt-5 text-xs text-[#98a19a] sm:flex-row sm:items-center"
            >
                <p>
                    Built for clear releases. Parsing is deterministic;
                    rewriting is optional.
                </p>
                <p class="font-mono">LARAVEL · VUE · GEMINI-READY</p>
            </footer>
        </main>
    </div>
</template>
