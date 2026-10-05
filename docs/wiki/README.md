# Wiki source

The Markdown files in this directory are the source of the
[Dalue wiki](https://github.com/uuur86/dalue/wiki).

The `Publish wiki` workflow (`.github/workflows/wiki.yml`) copies them to the
wiki repository whenever they change on `main`. It can also be started manually
from the **Actions** tab. Pages edited only on the wiki are replaced or removed
by the next publish, so always edit the files here.

- A file name becomes the page title: `Getting-Started.md` → *Getting Started*.
- Link to other pages by file name without the extension: `[Collections](Collections)`.
- `_Sidebar.md` and `_Footer.md` are shown on every page.
- This `README.md` is not published.

Before the first publish, enable the wiki in **Settings → Features → Wikis**
and save any page once in the GitHub UI; GitHub creates the wiki repository
only at that point.
