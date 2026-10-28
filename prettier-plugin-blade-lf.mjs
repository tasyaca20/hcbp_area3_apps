/**
 * Prettier plugin wrapper: normalizes line endings to LF.
 *
 * Why this exists:
 * `@shufo/prettier-plugin-blade` delegates to `blade-formatter`, but never forwards
 * Prettier's `endOfLine` option. `blade-formatter` then falls back to its own
 * default (`os.EOL`), which is CRLF on Windows. As a result Prettier reports its
 * own output as unformatted (`--check` never passes) and files end up with mixed
 * CRLF/LF line endings.
 *
 * This wrapper post-processes the printed output so it honours Prettier's
 * `endOfLine` setting (defaulting to `lf`). See `.prettierrc.json`.
 */
import * as bladePlugin from '@shufo/prettier-plugin-blade';

const EOL_MAP = {
    lf: '\n',
    crlf: '\r\n',
    cr: '\r',
};

const bladeParser = bladePlugin.parsers.blade;
const bladePrinter = bladePlugin.printers['blade-format'];

export const languages = bladePlugin.languages;
export const defaultOptions = bladePlugin.defaultOptions;
export const options = bladePlugin.options;

export const parsers = {
    blade: {
        ...bladeParser,
        // Normalize the source so the parser never sees stray CR characters.
        preprocess: (text, options) => {
            const normalized = text.replace(/\r\n?/g, '\n');
            return bladeParser.preprocess
                ? bladeParser.preprocess(normalized, options)
                : normalized;
        },
    },
};

export const printers = {
    'blade-format': {
        ...bladePrinter,
        print: (path, options, print) => {
            const output = bladePrinter.print(path, options, print);

            if (typeof output !== 'string') {
                return output;
            }

            // Respect Prettier's endOfLine; fall back to LF.
            const eol = EOL_MAP[options.endOfLine] ?? (options.endOfLine === 'auto' ? '\n' : '\n');

            return output.replace(/\r\n?/g, '\n').replace(/\n/g, eol);
        },
    },
};

export default {
    ...bladePlugin,
    parsers,
    printers,
};
