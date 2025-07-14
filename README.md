# YouTube Middleware Wordpress plugin

To cache and serve results of YouTube API queries.

1. Install the plugin.
2. Set your API key in its settings page (alternatively provide it to each API query).
3. Enabled the endpoints you want to enable.
4. Optionally apply parameter restrictions in settings.
5. Use the REST API endpoints.

<img width="948" height="579" alt="youtube-middleware-settings" src="https://github.com/user-attachments/assets/0f2b4f44-1e30-48b8-9860-341bc54a712c" />

# API endpoints

All API endpoints accept the following common parameters:

| Parameter     | Comment                     | Type    | Required | Default         |
| ------------- | --------------------------- | ------- | -------- | --------------- |
| key           | The API key                 | string  | No       | _from settings_ |
| cacheDuration | Seconds to cache result for | integer | No       | 30 minutes      |
| timeout       | Request timeout in seconds  | integer | No       | 10              |

All other parameters are passed as is to the relevant [YouTube Data API endpoint](https://developers.google.com/youtube/v3/docs).

The list of supported endpoints is:

- GET `/wp-json/youtube-middleware/v1/captions` &#x2192; [Captions: list](https://developers.google.com/youtube/v3/docs/captions/list)
- GET `/wp-json/youtube-middleware/v1/channelSections` &#x2192; [ChannelSections: list](https://developers.google.com/youtube/v3/docs/channelSections/list)
- GET `/wp-json/youtube-middleware/v1/channels` &#x2192; [Channels: list](https://developers.google.com/youtube/v3/docs/channels/list)
- GET `/wp-json/youtube-middleware/v1/comments` &#x2192; [Comments: list](https://developers.google.com/youtube/v3/docs/comments/list)
- GET `/wp-json/youtube-middleware/v1/commentThreads` &#x2192; [CommentThreads: list](https://developers.google.com/youtube/v3/docs/commentThreads/list)
- GET `/wp-json/youtube-middleware/v1/playlistItems` &#x2192; [PlaylistItems: list](https://developers.google.com/youtube/v3/docs/playlistItems/list)
- GET `/wp-json/youtube-middleware/v1/playlists` &#x2192; [Playlists: list](https://developers.google.com/youtube/v3/docs/playlists/list)
- GET `/wp-json/youtube-middleware/v1/search` &#x2192; [Search: list](https://developers.google.com/youtube/v3/docs/search/list)
- GET `/wp-json/youtube-middleware/v1/videoCategories` &#x2192; [VideoCategories: list](https://developers.google.com/youtube/v3/docs/videoCategories/list)
- GET `/wp-json/youtube-middleware/v1/videos` &#x2192; [Videos: list](https://developers.google.com/youtube/v3/docs/videos/list)
