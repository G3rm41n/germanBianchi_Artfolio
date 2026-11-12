<?php

namespace App\Services\Embed\Drivers;

class GoogleDocsDriver extends AbstractEmbedDriver
{
    public function getProviderName(): string
    {
        return 'google_docs';
    }

    public function matches(string $host, string $url): bool
    {
        return in_array($host, ['drive.google.com', 'docs.google.com']);
    }

    public function process(string $url, string $host): ?array
    {
        $title = null;
        $embedHtml = null;
        $imageUrl = null;

        // 1. Google Drive File: drive.google.com/file/d/{FILE_ID}/view
        if (preg_match('#drive\.google\.com/file/d/([a-zA-Z0-9_-]+)#i', $url, $m)) {
            $fileId = $m[1];
            $embedHtml = '<iframe src="https://drive.google.com/file/d/' . $fileId . '/preview" class="w-full h-[600px] rounded-xl border border-gray-200 dark:border-gray-700" allow="autoplay"></iframe>';
            $imageUrl = 'https://drive.google.com/thumbnail?id=' . $fileId . '&sz=w1000';
            $title = 'Google Drive Document';
        }
        // 2. Google Docs Document: docs.google.com/document/d/{DOC_ID}/...
        elseif (preg_match('#docs\.google\.com/document/d/([a-zA-Z0-9_-]+)#i', $url, $m)) {
            $docId = $m[1];
            $embedHtml = '<iframe src="https://docs.google.com/document/d/' . $docId . '/preview" class="w-full h-[650px] rounded-xl border border-gray-200 dark:border-gray-700"></iframe>';
            $title = 'Google Document';
        }
        // 3. Google Presentations: docs.google.com/presentation/d/{PRES_ID}/...
        elseif (preg_match('#docs\.google\.com/presentation/d/([a-zA-Z0-9_-]+)#i', $url, $m)) {
            $presId = $m[1];
            $embedHtml = '<iframe src="https://docs.google.com/presentation/d/' . $presId . '/embed?start=false&loop=false&delayms=3000" class="w-full aspect-[16/9] rounded-xl border border-gray-200 dark:border-gray-700" allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>';
            $title = 'Google Slides Presentation';
        }
        // 4. Google Sheets: docs.google.com/spreadsheets/d/{SHEET_ID}/...
        elseif (preg_match('#docs\.google\.com/spreadsheets/d/([a-zA-Z0-9_-]+)#i', $url, $m)) {
            $sheetId = $m[1];
            $embedHtml = '<iframe src="https://docs.google.com/spreadsheets/d/' . $sheetId . '/preview" class="w-full h-[600px] rounded-xl border border-gray-200 dark:border-gray-700"></iframe>';
            $title = 'Google Spreadsheet';
        }

        // Intentar obtener el título real scrapeando og:title
        $pageRes = $this->safeHttpGet($url);
        if ($pageRes) {
            $ogTitle = $this->extractMetaTag($pageRes->body(), 'og:title');
            if ($ogTitle) {
                $title = $this->cleanTitle($ogTitle, ['- Google Docs', '- Google Drive', '- Google Slides', '- Google Sheets']);
            }
        }

        return [
            'provider'      => $this->getProviderName(),
            'category'      => 'doc',
            'render_type'   => 'iframe',
            'embed_html'    => $embedHtml,
            'thumbnail_url' => $imageUrl,
            'title'         => $title,
        ];
    }
}

