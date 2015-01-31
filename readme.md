#NoodleDoor
A backdoor-like php application that can be uploaded to your website and help you manage it whilst not at home.

Or you know, maybe it's not your site, whatever: minor details.

##Todo
- File System
  - Detect things that don't have to be archived before downloading
  - Change file permissions
  - View root dir
- Command line
  - Execute commands
  - See output of commands
- Database
  - Search file system for password
    - Search for files named mysql
    - Search within files for mysql functions
  - View tables
  - View rows
  - Edit rows
  - Delete rows
  - Edit tables
  - Delete tables
  - Empty tables
  - Download tables
  - Edit databases
  - Drop databases
  - Download databases
  - Execute SQL
- Overall
  - Different versions
    - Hacker
      - Polymorphing (add stuff to different locations in file to change signature)
      - Copy itself (select folder to have NoodleDoor placed into all subsequent folders)
      - Database password detection (scan file system to see if passwords to database can be found)
      - From email detection (scan file system to see what email is used to send emails from the system)
      - Themeless (would not have bootstrap styling as that would be something anti-malware could look for)
