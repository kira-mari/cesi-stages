<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8"/>
	<xsl:template match="/">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<title>XML Sitemap - CESI Stage</title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<style type="text/css">
					body {
						font-family: 'Inter', system-ui, -apple-system, sans-serif;
						background-color: #0f0f1a;
						color: #ffffff;
						margin: 0;
						padding: 40px;
					}
					a {
						color: #6366f1;
						text-decoration: none;
					}
					a:hover {
						text-decoration: underline;
						color: #06b6d4;
					}
					h1 {
						font-size: 24px;
						margin-bottom: 10px;
						background: linear-gradient(135deg, #FFF 0%, #a5b4fc 100%);
						-webkit-background-clip: text;
						-webkit-text-fill-color: transparent;
					}
					p.desc {
						color: #94a3b8;
						font-size: 14px;
						margin-bottom: 30px;
					}
					table {
						border-collapse: collapse;
						width: 100%;
						background: #1a1a2e;
						border-radius: 12px;
						overflow: hidden;
						box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
						border: 1px solid rgba(255, 255, 255, 0.1);
					}
					th {
						background-color: rgba(99, 102, 241, 0.1);
						color: #e2e8f0;
						text-align: left;
						padding: 16px;
						font-weight: 600;
						font-size: 14px;
						border-bottom: 1px solid rgba(255, 255, 255, 0.1);
					}
					td {
						padding: 14px 16px;
						font-size: 14px;
						color: #cbd5e1;
						border-bottom: 1px solid rgba(255, 255, 255, 0.05);
					}
					tr:last-child td {
						border-bottom: none;
					}
					tr:hover td {
						background-color: rgba(255, 255, 255, 0.02);
					}
					.priority-high {
						color: #4ade80;
						font-weight: 500;
					}
				</style>
			</head>
			<body>
				<h1>XML Sitemap</h1>
				<p class="desc">Ce sitemap XML est généré pour aider les moteurs de recherche à indexer le contenu du site.</p>
				
				<table>
					<thead>
						<tr>
							<th>URL</th>
							<th>Priorité</th>
							<th>Fréquence</th>
							<th>Dernière modif.</th>
						</tr>
					</thead>
					<tbody>
						<xsl:for-each select="sitemap:urlset/sitemap:url">
							<tr>
								<td>
									<a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
								</td>
								<td>
									<xsl:value-of select="sitemap:priority"/>
								</td>
								<td>
									<xsl:value-of select="sitemap:changefreq"/>
								</td>
								<td>
									<xsl:value-of select="sitemap:lastmod"/>
								</td>
							</tr>
						</xsl:for-each>
					</tbody>
				</table>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
