<?php

// FUnctions to be provided to CHATGPT

$FUNCTIONS = [
    [
        "name" => "Inspect",
        "description" => "Look at or Inspects NPC, Actor, or being",
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Target NPC, Actor, or being",
                ]
            ],
            "required" => ["target"],
        ],
    ],
     [
        "name" => "InspectSurroundings",
        "description" => "Looks for new locations or targets nearby",
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
        "name" => "MoveTo",
        "description" => "Moves to a visible location or visible target, also used to guide Plugineer to a target o location.",
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Visible Target NPC, Actor, or being, or location.",
                ]
            ],
            "required" => ["target"],
        ],
    ],
    [
        "name" => "OpenInventory",
        "description" => "Initiates trading or exchange items with Plugineer",
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
        "description" => "Engages combat with actor, npc or being.Subject to moral interpretations ",
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
        "description" => "Moves to and follow a NPC, an actor or being",
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
        "description" => "Search in Herika\'s inventory.",
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "item to look for, if empty all items will be returned",
                ]
            ],
            "required" =>[]
        ]
    ],
     [
        "name" => "SheatheWeapon",
        "description" => "Sheates current weapon",
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Keep it blank",
                ]
            ],
            "required" =>[]
        ]
    ],
     [
        "name" => "Relax",
        "description" => "Makes Herika to stop current action and relax herself",
        "parameters" => [
            "type" => "object",
            "properties" => [
                "target" => [
                    "type" => "string",
                    "description" => "Keep it blank",
                ]
            ],
            "required" =>[]
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




?>
