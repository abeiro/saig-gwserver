<?php

// Functions to be provided to OpenAI

$ENABLED_FUNCTIONS=[
    'Inspect',
    'LookAt',
    'InspectSurroundings',
    'MoveTo',
    'OpenInventory',
    'Attack',
    'Follow',
    'CheckInventory',
    'SheatheWeapon',
    'Relax',
    'LeadTheWayTo',
    'TakeASeat',
    'ReadQuestJournal',
    'SetSpeed',
    'GetDateTime',
    'SearchDiary',
    'SetCurrentTask'
];



$F_TRANSLATIONS["Inspect"]="LOOK at or Inspects NPC, Actor, or being OUTFIT and GEAR";
$F_TRANSLATIONS["LookAt"]="LOOK at or Inspects NPC, Actor, or being OUTFIT and GEAR";
$F_TRANSLATIONS["InspectSurroundings"]="Looks for beings nearby";
$F_TRANSLATIONS["MoveTo"]= "Walk to a visible building or visible actor, also used to guide {$GLOBALS["PLAYER_NAME"]} to a actor or building.";
$F_TRANSLATIONS["OpenInventory"]="Initiates trading or exchange items with {$GLOBALS["PLAYER_NAME"]}";
$F_TRANSLATIONS["Attack"]="Attacks actor, npc or being. but always avoid the deaths of innocent actors.";
$F_TRANSLATIONS["Follow"]="Moves to and follow a NPC, an actor or being";
$F_TRANSLATIONS["CheckInventory"]="Search in {$GLOBALS["HERIKA_NAME"]}\'s inventory, backpack or pocket";
$F_TRANSLATIONS["SheatheWeapon"]="Sheates current weapon";
$F_TRANSLATIONS["Relax"]="Makes{$GLOBALS["HERIKA_NAME"]} to stop current action and relax herself";
$F_TRANSLATIONS["LeadTheWayTo"]="Only use if {$GLOBALS["PLAYER_NAME"]} explicitly orders it. Guide {$GLOBALS["PLAYER_NAME"]} to a Town o City. ";
$F_TRANSLATIONS["TakeASeat"]="{$GLOBALS["HERIKA_NAME"]} seats in nearby chair or furniture ";
$F_TRANSLATIONS["ReadQuestJournal"]="Only use if {$GLOBALS["PLAYER_NAME"]} explicitly ask for a quest. Get info about current quests";
$F_TRANSLATIONS["SetSpeed"]="Set {$GLOBALS["HERIKA_NAME"]} speed when moving or travelling";
$F_TRANSLATIONS["GetDateTime"]="Get Current Date and Time";
$F_TRANSLATIONS["SearchDiary"]="Read {$GLOBALS["HERIKA_NAME"]}'s diary to make her remember something. Search in diary index";
$F_TRANSLATIONS["SetCurrentTask"]="Set the current plan of action or task or quest";
$F_TRANSLATIONS["WriteIntoDiary"]="Summarize briefly the recent events and dialogues and write them down in Herika's diary.";
$F_TRANSLATIONS["ReadDiaryPage"]="Read {$GLOBALS["HERIKA_NAME"]}'s diary to access a specific topic";

if (isset($GLOBALS["CORE_LANG"]))
	if (file_exists(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$GLOBALS["CORE_LANG"].DIRECTORY_SEPARATOR."functions.php")) 
		require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$GLOBALS["CORE_LANG"].DIRECTORY_SEPARATOR."functions.php");
    
    
    
$FUNCTIONS = [
    [
        "name" => "Inspect",
        "description" => $F_TRANSLATIONS["Inspect"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Target NPC, Actor, or being",
                    "enum" => $FUNCTION_PARM_INSPECT

                ]
            ],
            "required" => ["target"],
        ],
    ],
    [
        "name" => "InspectSurroundings",
        "description" => $F_TRANSLATIONS["InspectSurroundings"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Keep it blank",
                ]
            ],
            "required" => []
        ],
    ],
    [
        "name" => "LookAt",
        "description" => $F_TRANSLATIONS["Inspect"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Target NPC, Actor, or being",
                    "enum" => $FUNCTION_PARM_INSPECT

                ]
            ],
            "required" => ["target"],
        ],
    ],
    [
        "name" => "MoveTo",
        "description" => $F_TRANSLATIONS["MoveTo"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Visible Target NPC, Actor, or being, or building.",
                    "enum" => $FUNCTION_PARM_MOVETO
                ]
            ],
            "required" => ["target"],
        ],
    ],
    [
        "name" => "OpenInventory",
        "description" => $F_TRANSLATIONS["OpenInventory"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Keep it blank",
                ]
            ],
            "required" => []
        ],
    ],
    [
        "name" => "Attack",
        "description" => $F_TRANSLATIONS["Attack"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Target NPC, Actor, or being",
                ]
            ],
            "required" => ["target"],
        ]
    ],
    [
        "name" => "Follow",
        "description" => $F_TRANSLATIONS["Follow"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Target NPC, Actor, or being",
                ]
            ],
            "required" => ["target"],
        ]
    ],
    [
        "name" => "CheckInventory",
        "description" => $F_TRANSLATIONS["CheckInventory"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "item to look for, if empty all items will be returned",
                ]
            ],
            "required" => []
        ]
    ],
    [
        "name" => "SheatheWeapon",
        "description" => $F_TRANSLATIONS["SheatheWeapon"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Keep it blank",
                ]
            ],
            "required" => []
        ]
    ],
    [
        "name" => "Relax",
        "description" => $F_TRANSLATIONS["Relax"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Keep it blank",
                ]
            ],
            "required" => []
        ]
    ],
    [
        "name" => "LeadTheWayTo",
        "description" => $F_TRANSLATIONS["LeadTheWayTo"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "location" => [
                    "type" => "string",
                    "description" => "Town or City to travel to, only if {$GLOBALS["PLAYER_NAME"]} explicitly orders it"
                    
                ]
            ],
            "required" => ["location"]
        ]
    ],
    [
        "name" => "TakeASeat",
        "description" => $F_TRANSLATIONS["TakeASeat"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Keep it blank",
                ]
            ],
            "required" => [""]
        ]
    ],
    [
        "name" => "ReadQuestJournal",
        "description" => $F_TRANSLATIONS["ReadQuestJournal"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "id_quest" => [
                    "type" => "string",
                    "description" => "Specific quest to get info for, or blank to get all",
                ]
            ],
            "required" => [""]
        ]
    ],
    [
        "name" => "SetSpeed",
        "description" => $F_TRANSLATIONS["SetSpeed"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "speed" => [
                    "type" => "string",
                    "description" => "Speed",
                    "enum" => ["run", "fastwalk", "jog", "walk"]
                ]

            ],
            "required" => ["speed"]
        ]
    ],
    [
        "name" => "GetDateTime",
        "description" => $F_TRANSLATIONS["GetDateTime"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "datestring" => [
                    "type" => "string",
                    "description" => "Formatted date and time",
                ]

            ],
            "required" => []
        ]
    ],
    [
        "name" => "SearchDiary",
        "description" => $F_TRANSLATIONS["SearchDiary"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "keyword" => [
                    "type" => "string",
                    "description" => "keyword to search in full-text query syntax",
                ]
            ],
            "required" => [""]
        ]
    ],
    [
        "name" => "SetCurrentTask",
        "description" => $F_TRANSLATIONS["SetCurrentTask"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "description" => [
                    "type" => "string",
                    "description" => "Short description of current task talked by the party",
                ]
            ],
            "required" => ["description"]
        ]
    ], 
    /*[
        "name" => "GetTopicInfo",
        "description" => "Get information about a topic or character on Herika's long-term memory.",
        "parameters" => [
            "type" => "object",
            "properties" => [
                "topic" => [
                    "type" => "string",
                    "description" => "Topic or Characters",
                ]
            ],
            "required" =>["topic"]
        ]
    ]*/
];


$FUNCTIONS_SPECIAL_CONTEXT = [

    [
        "name" => "WriteIntoDiary",
        "description" => $F_TRANSLATIONS["WriteIntoDiary"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "topic" => [
                    "type" => "string",
                    "description" => "Suggested topic name",
                ],
                "content" => [
                    "type" => "string",
                    "description" => "The summarized content"
                ],
                "tags" => [
                    "type" => "string",
                    "description" => "Relevant tags for later search"
                ],
                "people" => [
                    "type" => "string",
                    "description" => "Related People"
                ],
                "location" => [
                    "type" => "string",
                    "description" => "Location"
                ]


            ],
            "required" => ["topic", "content", "tags", "people","location"]
        ]
    ]
];


$FUNCTIONS_GHOSTED =  [
        "name" => "ReadDiaryPage",
        "description" => $F_TRANSLATIONS["ReadDiaryPage"],
        "parameters" => [
            "type" => "object",
            "properties" => [
                "page" => [
                    "type" => "string",
                    "description" => "topic to search in full-text query syntax",
                ]
            ],
            "required" => ["topic"]
        ]
    ]
    ;

// Delete non wanted functions    
foreach ($FUNCTIONS as $n=>$v)
    if (!in_array($v["name"],$ENABLED_FUNCTIONS)) {
            unset($FUNCTIONS[$n]);
    }



?>
