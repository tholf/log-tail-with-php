View log tail in web browser with PHP.
---
Tail a log with file offset stored in the session. Tracking the file offset will allow for transferring only the updated entries.

Tailing will also handle when the file is truncated.


Installation
---
Simply copy to PHP aware server location and update the $log to the absolute path of your log file.
