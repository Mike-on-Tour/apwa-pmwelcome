# pmwelcome

This version (1.1.0) is an updated clone of the `apwa/pmwelcome` extension which - as far as I know - is kind of abandoned and which I've found in an
inoperable state and which I updated to operate under phpbb 3\.2 and 3\.3

### Installation

Download from this repository all the subdirectories and their files to your PC or alternatively the zip package from [phpbb.de][] and unzip it.
Using a good ftp program such as filezilla or WinSCP (which I prefer for Windows systems) upload the files to your board into the `root/ext`folder.
After the successfully finished upload you should have this new directory structure `root/ext/apwa/pmwelcome` and within the `pmwelcome` directory
all the files and subdirectories listed here.  
Now you can start your board and sign into the ACP. Go to the `Customise` tab and look in the `Disabled Extensions` section for the line labeled
`PM Welcome` and click `Enable`. After having successfully enabled the extension switch to the `General` tab and look under `Board Configuration` for 
`Welcome Message`. Now you can start with the chapter 'How to use'.

[phpbb.de]: https://www.phpbb.de/community/viewtopic.php?f=149&t=244671&p=1404854#p1404854 "phpbb.de"

### How to use

Clicking this link will open the settings. Beneath the title you will find the section where you can write and edit your welcome message. The textarea
should be empty since this is the first time you open the settings page. Left of it you will see a row with bbcode buttons and an array to select text colors.
At the head of this section is a list with a short description of al the tokens you can use to edit your welcome message. If you are curious how it will
look like  for the addressee just select the `Preview` button und you will see this beneath the textarea. Since nobody knows the data of the later addressee
the user related tokens you used in the text are replaced with your data. If you are satisfied with your new welcome message please scroll down to the next
section.  
In the `Welcome message settings` section you can choose the id of the user on whose behalf the message is sent, by default it should show the digit '2'
(which is the founder and by default administrator of the board) and the user name of that individual. If that setting is to your satisfaction you
can go to the next field and write the subject of your welcome message.  
The only thing left to do is to select the `Submit` button to store your input.  
Congratulations, you have set up your welcome message and are done.
From now on all your newly registered (and activated) users will find your welcome message in their `Private messages` Inbox after their first login.

### Principle of Operation

If you are interested in what is done behind the scenes of the extension you should read this chapter.  
But before we start with the bolts and nuts explanation we should take a quick look on how phpbb is handling registering and activation of new users.
You as the administrator can influence what happens by selecting the account activation method in the ACP's `General` tab selection
`User registration settings`. The first setting `Account activation` offers you four choices within a dropdown field.  
The first one, 'Disable registration' obviously is of no interest to us so we skip it.  
The second item reads 'No activation (immidiate access)' which means that a user just registers and after finishing that process is a member of your board.
This is highly anonymous since nobody checks for a valid email address and therefore your boards doors are wide open for e.g. spammers.  
The third item in this list reads 'By user (email verification)' which means that a newly registered user gets an email with an activation link which
leads him or her to the activation routine (though this routine is not displayed, it just notifies the user that he/she has been successfully activated).  
The last one in this list is 'By admin' which means that you have to activate every new user manually through the ACP.  
  
With that knowledge we see that there are two instances of activation and one of solely registering. Registering and activation are activities within phpbb
which each triggers a so called 'event', pmwelcome monitors these events through its listener routine and starts working when one of these events is called.  
Every user registering with your board triggers the event `core.ucp_register_register_after` which is used to get some user data and send the welcome
message in case you as board admin chose to grant immidiate access to your board.  
Now I can hear you asking why this method isn't used for all above mentioned cases. Good question, but imagine we have a user giving a false email address
during registration, this user will never be activated if you selected the 'By user (email verifiction)' method and therefore will never be a member of
your board, but has already received the welcome message. Since you wouldn't want to welcome a guy who is no member we have to use the activation routine's
event, which is `core.user_active_flip_after`. This event is triggered whenever a user gets activated or deactivated. In case a user gets activated
pmwelcome gets the needed user data and sends the welcome message. If user activation by email is your selected method only those newly registered users
with a valid email address will be welcomed to your board. Any others will never be able to use the activation code since they will not receive the
boards activation email and thus never receive your welcome message.  
The same happens if you selected activation by the admin, a newly registered user you won't activate for any number of reasons will never get the
welcome message.  
In addition, I'm pretty, but not a hundred percent, certain that deactivated users can not receive private messages so we have to wait until they are
activated to send our welcome message.
