<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeoPageData;

class SeoPageSeeder extends Seeder
{
    public function run()
    {
        $configArr = _getSiteSetting();
        $logo = _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'];
        $siteName = $configArr['web_name'];
        $baseUrl  = url('/');

        $pages = [

            'home' => [
                'title' => "$siteName – Trusted Matrimony Site to Find Your Life Partner",
                'desc'  => "Join $siteName, the most trusted matrimonial platform to find verified bride and groom profiles by religion, caste, city and profession.",
                'keywords' => "matrimony, matrimonial site, bride, groom, marriage, matchmaking",
                'schema' => [
                    "@context" => "https://schema.org",
                    "@graph" => [
                        [
                            "@type" => "Organization",
                            "name" => $siteName,
                            "url" => $baseUrl,
                            "logo" => $logo,
                        ],
                        [
                            "@type" => "WebSite",
                            "url" => $baseUrl,
                            "potentialAction" => [
                                "@type" => "SearchAction",
                                "target" => "$baseUrl/search?query={search_term_string}",
                                "query-input" => "required name=search_term_string"
                            ]
                        ]
                    ]
                ]
            ],

            'login' => [
                'title' => "Login to $siteName Matrimony",
                'desc'  => "Login to your matrimonial account to view matches, send interests and chat securely.",
                'keywords' => "matrimony login, member login",
                'schema' => ["@context" => "https://schema.org", "@type" => "WebPage"]
            ],

            'register' => [
                'title' => "Register Free on $siteName Matrimony",
                'desc'  => "Create your free matrimonial profile and start finding your perfect life partner.",
                'keywords' => "register matrimony, create profile",
                'schema' => ["@context" => "https://schema.org", "@type" => "WebPage"]
            ],

            'forgot-password' => [
                'title' => "Recover Your Matrimony Account Password",
                'desc'  => "Reset your matrimony account password securely.",
                'keywords' => "forgot password matrimony",
                'schema' => ["@context" => "https://schema.org", "@type" => "WebPage"]
            ],

            'quick-search' => [
                'title' => "Quick Search Bride & Groom Profiles",
                'desc'  => "Quickly search verified matrimonial profiles by basic filters.",
                'keywords' => "quick matrimony search",
                'schema' => [
                    "@context" => "https://schema.org",
                    "@type" => "SearchAction",
                    "target" => "$baseUrl/quick-search?query={search_term_string}",
                    "query-input" => "required name=search_term_string"
                ]
            ],

            'advance-search' => [
                'title' => "Advanced Matrimony Search",
                'desc'  => "Use advanced filters to find perfect bride or groom profiles.",
                'keywords' => "advanced matrimony search filters",
                'schema' => ["@context" => "https://schema.org", "@type" => "SearchAction"]
            ],

            'keyword-search' => [
                'title' => "Keyword Search Matrimonial Profiles",
                'desc'  => "Search profiles using keywords like profession, city, caste.",
                'keywords' => "keyword matrimony search",
                'schema' => ["@context" => "https://schema.org", "@type" => "SearchAction"]
            ],

            'id-search' => [
                'title' => "Search Matrimony Profile by ID",
                'desc'  => "Find a specific bride or groom profile using Matrimony ID.",
                'keywords' => "profile id search matrimony",
                'schema' => ["@context" => "https://schema.org", "@type" => "SearchAction"]
            ],

            'membership-plan' => [
                'title' => "$siteName Matrimony Membership Plans & Pricing",
                'desc'  => "View affordable matrimony membership plans to connect directly with matches.",
                'keywords' => "matrimony plans pricing",
                'schema' => [
                    "@context" => "https://schema.org",
                    "@type" => "Service",
                    "name" => "Matrimony Membership Plans",
                    "provider" => [
                        "@type" => "Organization",
                        "name" => $siteName
                    ]
                ]
            ],

            'about-us' => [
                'title' => "About $siteName – Trusted Matrimony Platform",
                'desc'  => "$siteName is a trusted matrimonial platform helping brides and grooms find their perfect life partners with verified profiles and smart matchmaking.",
                'keywords' => "about matrimony, about $siteName, matrimonial company",
                'schema' => [
                    "@context" => "https://schema.org",
                    "@type" => "AboutPage",
                    "name" => "About $siteName",
                    "url" => "$baseUrl/about-us",
                    "description" => "$siteName helps people find life partners through verified matrimonial profiles.",
                    "publisher" => [
                        "@type" => "Organization",
                        "name" => $siteName,
                        "logo" => [
                            "@type" => "ImageObject",
                            "url" => $logo
                        ]
                    ]
                ]
            ],

            'faq' => [
                'title' => "Matrimony FAQs – Common Questions Answered",
                'desc'  => "Find answers to common questions about registration, profile search, membership plans and safety on $siteName matrimony.",
                'keywords' => "matrimony faq, matrimonial questions, help",
                'schema' => [
                    "@context" => "https://schema.org",
                    "@type" => "FAQPage",
                    "mainEntity" => [
                        [
                            "@type" => "Question",
                            "name" => "Is registration free on $siteName?",
                            "acceptedAnswer" => [
                                "@type" => "Answer",
                                "text" => "Yes, you can register and create your matrimonial profile for free."
                            ]
                        ],
                        [
                            "@type" => "Question",
                            "name" => "How can I search for bride or groom profiles?",
                            "acceptedAnswer" => [
                                "@type" => "Answer",
                                "text" => "You can use quick search, advanced search, keyword search or ID search options."
                            ]
                        ],
                        [
                            "@type" => "Question",
                            "name" => "Are profiles verified?",
                            "acceptedAnswer" => [
                                "@type" => "Answer",
                                "text" => "Yes, we manually and automatically verify profiles to ensure authenticity."
                            ]
                        ],
                        [
                            "@type" => "Question",
                            "name" => "What are membership benefits?",
                            "acceptedAnswer" => [
                                "@type" => "Answer",
                                "text" => "Membership allows you to chat, view contact details and connect directly with matches."
                            ]
                        ]
                    ]
                ]
            ],

            'success-story' => [
                'title' => "Matrimony Success Stories – Happy Couples",
                'desc'  => "Read real success stories of couples who found love through our matrimony site.",
                'keywords' => "matrimony success stories",
                'schema' => ["@context" => "https://schema.org", "@type" => "Article"]
            ],

            'event' => [
                'title' => "Matrimony Events & Meetups",
                'desc'  => "Attend matrimony events and meet verified brides and grooms in person.",
                'keywords' => "matrimony events meetup",
                'schema' => ["@context" => "https://schema.org", "@type" => "Event"]
            ],

            'blog' => [
                'title' => "Matrimony Blog – Marriage Tips & Relationship Advice",
                'desc'  => "Read expert articles on marriage, matchmaking and relationships.",
                'keywords' => "marriage tips blog",
                'schema' => [
                    "@context" => "https://schema.org",
                    "@type" => "Blog",
                    "name" => "$siteName Blog",
                    "url" => "$baseUrl/blog"
                ]
            ],
            'contact-us' => [
                'title' => "Contact $siteName – Get Support for Your Matrimony Journey",
                'desc'  => "Contact $siteName for any help regarding profile creation, membership plans, matches, safety or technical support. Our team is here to assist you.",
                'keywords' => "contact matrimony, matrimony support, help $siteName, customer care",
                'schema' => [
                    "@context" => "https://schema.org",
                    "@type" => "ContactPage",
                    "name" => "Contact $siteName",
                    "url" => "$baseUrl/contact-us",
                    "description" => "Get in touch with $siteName matrimony support team for assistance.",
                    "publisher" => [
                        "@type" => "Organization",
                        "name" => $siteName,
                        "url" => $baseUrl,
                        "logo" => [
                            "@type" => "ImageObject",
                            "url" => $logo
                        ],
                        "contactPoint" => [
                            "@type" => "ContactPoint",
                            "contactType" => "customer support",
                            "areaServed" => "IN",
                            "availableLanguage" => ["English", "Hindi"]
                        ]
                    ]
                ]
            ],
            'blog-detail' => [
                'title' => "Matrimony Blog Detail",
                'desc'  => "Read this informative matrimonial article.",
                'keywords' => "matrimony article",
                'schema' => [
                    "@context" => "https://schema.org",
                    "@type" => "Article",
                    "headline" => "{{title}}",
                    "image" => "{{image}}",
                    "author" => ["@type" => "Person", "name" => $siteName],
                    "publisher" => [
                        "@type" => "Organization",
                        "name" => $siteName,
                        "logo" => ["@type" => "ImageObject", "url" => $logo]
                    ]
                ]
            ],

            'wedding-vendor' => [
                'title' => "Find Wedding Vendors Near You",
                'desc'  => "Discover best wedding vendors like photographers, decorators and caterers.",
                'keywords' => "wedding vendors",
                'schema' => ["@context" => "https://schema.org", "@type" => "LocalBusiness"]
            ],

            'advertisement' => [
                'title' => "Advertise With $siteName Matrimony",
                'desc'  => "Promote your wedding services to thousands of matrimony members.",
                'keywords' => "advertise wedding business",
                'schema' => ["@context" => "https://schema.org", "@type" => "WebPage"]
            ],

            'matrimony-dynamic' => [
                'title' => "{{name}} Matrimony – Find Verified {{name}} Bride & Groom Profiles",
                'desc'  => "Join $siteName {{name}} Matrimony to search verified {{name}} bride and groom profiles by city, profession, education and more. Register free and find your perfect match.",
                'keywords' => "{{name}} matrimony, {{name}} bride, {{name}} groom, {{name}} matrimonial site",
                'schema' => [
                    "@context" => "https://schema.org",
                    "@type" => "CollectionPage",
                    "name" => "{{name}} Matrimony",
                    "url" => "{{url}}",
                    "description" => "Verified {{name}} bride and groom profiles for marriage.",
                    "isPartOf" => [
                        "@type" => "WebSite",
                        "name" => $siteName,
                        "url" => $baseUrl
                    ]
                ]
            ],
        ];

        foreach ($pages as $slug => $data) {
            SeoPageData::updateOrCreate(
                ['page_slug' => $slug, 'lang_code' => 'en'],
                [
                    'page_title'       => ucwords(str_replace('-', ' ', $slug)),
                    'seo_title'        => $data['title'],
                    'seo_description'  => $data['desc'],
                    'seo_keywords'     => $data['keywords'],
                    'og_title'         => $data['title'],
                    'og_description'   => $data['desc'],
                    'og_type'          => 'website',
                    'meta_robots'      => 'index,follow',
                    'schema_json'      => $data['schema'],
                    'created_at'      => _getCurrentDate(),
                ]
            );
        }
    }
}
