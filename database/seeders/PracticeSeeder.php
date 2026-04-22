<?php

namespace Database\Seeders;

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use Illuminate\Database\Seeder;

class PracticeSeeder extends Seeder
{
    private const FOCUS_PROBLEMS = [
        ['en' => 'Anxiety', 'ru' => 'Тревога'],
        ['en' => 'Fatigue', 'ru' => 'Усталость'],
        ['en' => 'Focus', 'ru' => 'Фокус'],
        ['en' => 'Anger', 'ru' => 'Гнев'],
        ['en' => 'Autopilot', 'ru' => 'Автопилот'],
    ];

    private const EXPERIENCE_LEVELS = [
        ['en' => 'Beginner', 'ru' => 'Новичок'],
        ['en' => 'Intermediate', 'ru' => 'Средний'],
        ['en' => 'Advanced', 'ru' => 'Продвинутый'],
    ];

    private const MODULE_CHOICES = [
        ['en' => 'Main', 'ru' => 'Главный'],
        ['en' => 'Nutrition', 'ru' => 'Питание'],
        ['en' => 'All', 'ru' => 'Все'],
    ];

    private const MEDITATION_TYPES = [
        ['en' => 'Breath', 'ru' => 'Дыхание'],
        ['en' => 'Body', 'ru' => 'Тело'],
        ['en' => 'Observation', 'ru' => 'Наблюдение'],
        ['en' => 'Movement', 'ru' => 'Движение'],
        ['en' => 'Pause', 'ru' => 'Пауза'],
        ['en' => 'Space', 'ru' => 'Пространство'],
    ];

    /**
     * The day themes are synthesized from publicly available mindfulness guidance
     * and practice libraries from Mayo Clinic, NHS resources, the Mental Health
     * Foundation, and Mindful. Each guide keeps EN/RU copy in the seed so the
     * admin panel starts with translated content for every day.
     *
     * @var array<int, array{
     *     slug: string,
     *     duration: int,
     *     source_url: string,
     *     title: array{en: string, ru: string},
     *     description: array{en: string, ru: string}
     * }>
     */
    private const DAY_GUIDES = [
        1 => [
            'slug' => 'arrive-with-one-breath',
            'duration' => 60,
            'source_url' => 'https://www.mayoclinic.org/tests-procedures/meditation/in-depth/mindfulness-exercises/art-20046356',
            'title' => [
                'en' => 'Arrive With One Breath',
                'ru' => 'Вернуться одним дыханием',
            ],
            'description' => [
                'en' => 'Use one slow inhale and exhale to interrupt the rush of the day. Let the breath become your first anchor and return to it without judging yourself whenever attention drifts.',
                'ru' => 'Используйте один медленный вдох и выдох, чтобы прервать дневную спешку. Пусть дыхание станет вашим первым якорем, и мягко возвращайтесь к нему без осуждения, когда внимание уходит.',
            ],
        ],
        2 => [
            'slug' => 'three-breaths-one-pause',
            'duration' => 180,
            'source_url' => 'https://connect.mayoclinic.org/blog/living-with-mild-cognitive-impairment-mci/newsfeed-post/stop-practice-a-mindfulness-technique/',
            'title' => [
                'en' => 'Three Breaths, One Pause',
                'ru' => 'Три дыхания и одна пауза',
            ],
            'description' => [
                'en' => 'Stop what you are doing, take a short pause, and let three slower breaths settle the nervous system. Use the pause to notice what is happening before you move into the next task.',
                'ru' => 'Остановитесь, сделайте короткую паузу и позвольте трём более медленным дыханиям успокоить нервную систему. Используйте эту паузу, чтобы заметить, что происходит, прежде чем переходить к следующему делу.',
            ],
        ],
        3 => [
            'slug' => 'breathing-anchor-for-clarity',
            'duration' => 600,
            'source_url' => 'https://www.wyevalley.nhs.uk/services/community-services/pain-management-service/mindfulness-mp3-tracks.aspx',
            'title' => [
                'en' => 'Breathing Anchor for Clarity',
                'ru' => 'Дыхание как якорь для ясности',
            ],
            'description' => [
                'en' => 'Let breathing be the place you come back to when the mind races. Notice the air at the nose, chest, or belly and reset attention one breath at a time.',
                'ru' => 'Пусть дыхание станет местом, куда вы возвращаетесь, когда ум ускоряется. Замечайте воздух у носа, в груди или животе и перенастраивайте внимание по одному дыханию за раз.',
            ],
        ],
        4 => [
            'slug' => 'quick-body-check-in',
            'duration' => 180,
            'source_url' => 'https://www.mayoclinic.org/tests-procedures/meditation/in-depth/mindfulness-exercises/art-20046356',
            'title' => [
                'en' => 'Quick Body Check-In',
                'ru' => 'Быстрая проверка тела',
            ],
            'description' => [
                'en' => 'Scan from head to feet and notice tension, warmth, pressure, or ease. Nothing needs to be fixed first; the goal is to notice what is already here.',
                'ru' => 'Просканируйте тело от головы до стоп и отметьте напряжение, тепло, давление или лёгкость. Сначала ничего не нужно исправлять; цель в том, чтобы заметить то, что уже есть.',
            ],
        ],
        5 => [
            'slug' => 'short-body-scan',
            'duration' => 720,
            'source_url' => 'https://www.sheffieldtalkingtherapies.nhs.uk/first-steps-mindfulness-audio',
            'title' => [
                'en' => 'Short Body Scan',
                'ru' => 'Короткое сканирование тела',
            ],
            'description' => [
                'en' => 'Move attention through the body with curiosity and kindness. If sensations or emotions become intense, return to the breath as a safe anchor and continue at your own pace.',
                'ru' => 'Перемещайте внимание по телу с любопытством и добротой. Если ощущения или эмоции становятся слишком сильными, возвращайтесь к дыханию как к безопасному якорю и продолжайте в своём темпе.',
            ],
        ],
        6 => [
            'slug' => 'breath-and-body-together',
            'duration' => 480,
            'source_url' => 'https://www.guysandstthomas.nhs.uk/health-information/mindfulness-based-stress-reduction-mbsr/mbsr-exercises',
            'title' => [
                'en' => 'Breath and Body Together',
                'ru' => 'Дыхание и тело вместе',
            ],
            'description' => [
                'en' => 'Follow the breath while noticing how the body moves with each inhale and exhale. Let the mind settle by pairing attention with physical sensation.',
                'ru' => 'Следите за дыханием и одновременно замечайте, как тело движется с каждым вдохом и выдохом. Позвольте уму успокоиться, соединяя внимание с телесными ощущениями.',
            ],
        ],
        7 => [
            'slug' => 'three-minute-breathing-space',
            'duration' => 240,
            'source_url' => 'https://www.wyevalley.nhs.uk/services/community-services/pain-management-service/mindfulness-mp3-tracks.aspx',
            'title' => [
                'en' => 'The Three-Minute Breathing Space',
                'ru' => 'Трёхминутное дыхательное пространство',
            ],
            'description' => [
                'en' => 'Pause, notice what is present, gather attention on the breath, and then widen awareness to the whole body before continuing. Use it as a bridge between tasks or emotions.',
                'ru' => 'Сделайте паузу, отметьте то, что присутствует, соберите внимание на дыхании, а затем расширьте осознавание на всё тело перед тем, как продолжить. Используйте эту практику как мост между задачами или эмоциями.',
            ],
        ],
        8 => [
            'slug' => 'walking-without-going-anywhere',
            'duration' => 600,
            'source_url' => 'https://www.guysandstthomas.nhs.uk/health-information/mindfulness-based-stress-reduction-mbsr/mbsr-exercises',
            'title' => [
                'en' => 'Walking Without Going Anywhere',
                'ru' => 'Ходьба без цели',
            ],
            'description' => [
                'en' => 'Walk slowly with no destination and train awareness of balance, weight, and contact with the ground. Let each step bring you back to the present moment.',
                'ru' => 'Ходите медленно без конкретной цели и развивайте осознавание равновесия, веса тела и контакта с землёй. Пусть каждый шаг возвращает вас в настоящий момент.',
            ],
        ],
        9 => [
            'slug' => 'mindful-movement-to-wake-up',
            'duration' => 780,
            'source_url' => 'https://www.uhdb.nhs.uk/audio-resources-for-mindfulness/',
            'title' => [
                'en' => 'Mindful Movement to Wake Up',
                'ru' => 'Осознанное движение для пробуждения',
            ],
            'description' => [
                'en' => 'Combine gentle movement, breath, and stillness to energize the body and focus the mind. Move slowly enough to feel each shift of posture and rhythm.',
                'ru' => 'Соединяйте мягкое движение, дыхание и неподвижность, чтобы наполнить тело энергией и сфокусировать ум. Двигайтесь достаточно медленно, чтобы чувствовать каждое изменение позы и ритма.',
            ],
        ],
        10 => [
            'slug' => 'sitting-with-the-breath',
            'duration' => 660,
            'source_url' => 'https://www.wyevalley.nhs.uk/services/community-services/pain-management-service/mindfulness-mp3-tracks.aspx',
            'title' => [
                'en' => 'Sitting With the Breath',
                'ru' => 'Сидячая практика с дыханием',
            ],
            'description' => [
                'en' => 'Sit comfortably and notice the steady rhythm of breathing. When thinking pulls you away, acknowledge it and begin again from the next breath.',
                'ru' => 'Сядьте удобно и замечайте устойчивый ритм дыхания. Когда мысли уводят вас в сторону, признавайте это и начинайте заново со следующего дыхания.',
            ],
        ],
        11 => [
            'slug' => 'listen-to-sounds-come-and-go',
            'duration' => 480,
            'source_url' => 'https://www.guysandstthomas.nhs.uk/health-information/mindfulness-based-stress-reduction-mbsr/mbsr-exercises',
            'title' => [
                'en' => 'Listen to Sounds Come and Go',
                'ru' => 'Слушать звуки и отпускать',
            ],
            'description' => [
                'en' => 'Open attention to nearby and distant sounds without chasing or resisting them. Practice receiving experience instead of trying to control it.',
                'ru' => 'Откройте внимание для близких и дальних звуков, не цепляясь за них и не отталкивая их. Практикуйте принятие опыта вместо попытки всё контролировать.',
            ],
        ],
        12 => [
            'slug' => 'watch-thoughts-like-clouds',
            'duration' => 540,
            'source_url' => 'https://www.guysandstthomas.nhs.uk/health-information/mindfulness-based-stress-reduction-mbsr/mbsr-exercises',
            'title' => [
                'en' => 'Watch Thoughts Like Clouds',
                'ru' => 'Наблюдать мысли как облака',
            ],
            'description' => [
                'en' => 'Notice thoughts appearing and fading rather than treating each one as a command. Observation creates room between noticing and reacting.',
                'ru' => 'Замечайте, как мысли появляются и исчезают, не воспринимая каждую из них как приказ. Наблюдение создаёт пространство между замечанием и реакцией.',
            ],
        ],
        13 => [
            'slug' => 'name-the-emotion-soften-the-reaction',
            'duration' => 420,
            'source_url' => 'https://connect.mayoclinic.org/blog/living-with-mild-cognitive-impairment-mci/newsfeed-post/stop-practice-a-mindfulness-technique/',
            'title' => [
                'en' => 'Name the Emotion, Soften the Reaction',
                'ru' => 'Назвать эмоцию и смягчить реакцию',
            ],
            'description' => [
                'en' => 'Recognize emotions in the body and label them gently. Naming anger, fear, or worry can make it easier to respond with steadiness instead of impulse.',
                'ru' => 'Распознавайте эмоции в теле и мягко называйте их. Когда вы называете гнев, страх или тревогу, становится легче отвечать устойчиво, а не импульсивно.',
            ],
        ],
        14 => [
            'slug' => 'spacious-awareness',
            'duration' => 660,
            'source_url' => 'https://www.wyevalley.nhs.uk/services/community-services/pain-management-service/mindfulness-mp3-tracks.aspx',
            'title' => [
                'en' => 'Spacious Awareness',
                'ru' => 'Пространственное осознавание',
            ],
            'description' => [
                'en' => 'Expand from a narrow point of focus into a wider field that includes breath, body, thoughts, and sounds. Let awareness feel roomy and unforced.',
                'ru' => 'Расширяйтесь от узкой точки фокуса к более широкому полю, которое включает дыхание, тело, мысли и звуки. Позвольте осознаванию стать просторным и ненапряжённым.',
            ],
        ],
        15 => [
            'slug' => 'explore-difficulty-with-kindness',
            'duration' => 600,
            'source_url' => 'https://www.wyevalley.nhs.uk/services/community-services/pain-management-service/mindfulness-mp3-tracks.aspx',
            'title' => [
                'en' => 'Explore Difficulty With Kindness',
                'ru' => 'Исследовать трудность с добротой',
            ],
            'description' => [
                'en' => 'Turn toward discomfort carefully instead of bracing against it. Stay near the edges of intensity and return to the breath whenever you need support.',
                'ru' => 'Осторожно поворачивайтесь к дискомфорту вместо того, чтобы напрягаться против него. Оставайтесь рядом с краями интенсивности и возвращайтесь к дыханию всякий раз, когда нужна опора.',
            ],
        ],
        16 => [
            'slug' => 'stop-observe-proceed',
            'duration' => 60,
            'source_url' => 'https://connect.mayoclinic.org/blog/living-with-mild-cognitive-impairment-mci/newsfeed-post/stop-practice-a-mindfulness-technique/',
            'title' => [
                'en' => 'Stop, Observe, Proceed',
                'ru' => 'Остановись, наблюдай, продолжай',
            ],
            'description' => [
                'en' => 'Use the STOP sequence: stop, take slow breaths, observe what is happening, and proceed with the most helpful next step. This is a compact reset for stressful moments.',
                'ru' => 'Используйте последовательность STOP: остановитесь, сделайте медленные вдохи, наблюдайте за происходящим и продолжайте с самым полезным следующим шагом. Это компактная перезагрузка для напряжённых моментов.',
            ],
        ],
        17 => [
            'slug' => 'release-jaw-shoulders-and-belly',
            'duration' => 300,
            'source_url' => 'https://www.guysandstthomas.nhs.uk/health-information/mindfulness-based-stress-reduction-mbsr/mbsr-exercises',
            'title' => [
                'en' => 'Release Jaw, Shoulders, and Belly',
                'ru' => 'Расслабить челюсть, плечи и живот',
            ],
            'description' => [
                'en' => 'Notice where the body stores stress and invite those areas to soften on the out-breath. Even a short physical reset can change the tone of the day.',
                'ru' => 'Замечайте, где тело хранит стресс, и на выдохе мягко приглашайте эти области расслабиться. Даже короткая телесная перезагрузка может изменить тон всего дня.',
            ],
        ],
        18 => [
            'slug' => 'step-out-of-autopilot',
            'duration' => 120,
            'source_url' => 'https://www.mindful.org/a-two-minute-mindfulness-practice-to-unhijack-your-attention/',
            'title' => [
                'en' => 'Step Out of Autopilot',
                'ru' => 'Выйти из автопилота',
            ],
            'description' => [
                'en' => 'Check in with body, breath, and surroundings so the day does not pass in a blur. Use mindful noticing to re-enter the present on purpose.',
                'ru' => 'Проверьте, что происходит в теле, дыхании и вокруг вас, чтобы день не проходил в тумане. Используйте осознанное замечание, чтобы намеренно вернуться в настоящий момент.',
            ],
        ],
        19 => [
            'slug' => 'attention-reset-for-focus',
            'duration' => 480,
            'source_url' => 'https://www.mayoclinic.org/tests-procedures/meditation/in-depth/mindfulness-exercises/art-20046356',
            'title' => [
                'en' => 'Attention Reset for Focus',
                'ru' => 'Перезагрузка внимания для фокуса',
            ],
            'description' => [
                'en' => 'Train attention by returning to one chosen object each time the mind wanders. Repetition is the practice; refocusing is the skill you are building.',
                'ru' => 'Тренируйте внимание, возвращаясь к одному выбранному объекту всякий раз, когда ум отвлекается. Повторение и есть практика; возвращение и есть навык, который вы развиваете.',
            ],
        ],
        20 => [
            'slug' => 'ground-anxiety-in-the-present',
            'duration' => 300,
            'source_url' => 'https://www.mentalhealth.org.uk/explore-mental-health/a-z-topics/mindfulness',
            'title' => [
                'en' => 'Ground Anxiety in the Present',
                'ru' => 'Заземлить тревогу в настоящем',
            ],
            'description' => [
                'en' => 'Use breath and sensory awareness to come back from what-ifs and future stories. Feel the floor, notice the room, and let the body remind you that you are here now.',
                'ru' => 'Используйте дыхание и сенсорное осознавание, чтобы возвращаться из сценариев "а что если" и историй о будущем. Почувствуйте пол, отметьте комнату и позвольте телу напомнить вам, что вы здесь и сейчас.',
            ],
        ],
        21 => [
            'slug' => 'space-before-anger-speaks',
            'duration' => 240,
            'source_url' => 'https://newsnetwork.mayoclinic.org/discussion/mayo-mindfulness-meditation-is-good-medicine/',
            'title' => [
                'en' => 'Create Space Before Anger Speaks',
                'ru' => 'Создать пространство до того, как заговорит гнев',
            ],
            'description' => [
                'en' => 'Pause before reacting, feel the heat of anger in the body, and lengthen the exhale. The aim is not suppression but a wiser response.',
                'ru' => 'Сделайте паузу перед реакцией, почувствуйте жар гнева в теле и удлините выдох. Цель не в подавлении, а в более мудром ответе.',
            ],
        ],
        22 => [
            'slug' => 'move-through-fatigue-gently',
            'duration' => 600,
            'source_url' => 'https://www.uhdb.nhs.uk/audio-resources-for-mindfulness/',
            'title' => [
                'en' => 'Move Through Fatigue Gently',
                'ru' => 'Мягко пройти через усталость',
            ],
            'description' => [
                'en' => 'When energy is low, combine slower breathing with light movement and posture changes. Let gentle activation replace harsh self-pressure.',
                'ru' => 'Когда энергии мало, соединяйте более медленное дыхание с лёгким движением и сменой позы. Пусть мягкая активация заменит жёсткое давление на себя.',
            ],
        ],
        23 => [
            'slug' => 'nature-and-open-monitoring',
            'duration' => 480,
            'source_url' => 'https://www.mayoclinic.org/tests-procedures/meditation/in-depth/mindfulness-exercises/art-20046356',
            'title' => [
                'en' => 'Nature and Open Monitoring',
                'ru' => 'Природа и открытое наблюдение',
            ],
            'description' => [
                'en' => 'Practice outdoors or near a window and widen attention to light, temperature, sound, and space. Open monitoring can restore attention and soften mental load.',
                'ru' => 'Практикуйте на улице или у окна и расширяйте внимание на свет, температуру, звуки и пространство. Открытое наблюдение помогает восстановить внимание и уменьшить умственную перегрузку.',
            ],
        ],
        24 => [
            'slug' => 'befriend-the-present-moment',
            'duration' => 600,
            'source_url' => 'https://www.wyevalley.nhs.uk/services/community-services/pain-management-service/mindfulness-mp3-tracks.aspx',
            'title' => [
                'en' => 'Befriend the Present Moment',
                'ru' => 'Подружиться с настоящим моментом',
            ],
            'description' => [
                'en' => 'Meet the moment with friendliness instead of judgment. Kind attention lowers inner struggle and makes difficult states easier to hold.',
                'ru' => 'Встречайте момент с дружелюбием вместо осуждения. Доброе внимание уменьшает внутреннюю борьбу и помогает легче удерживать сложные состояния.',
            ],
        ],
        25 => [
            'slug' => 'notice-a-pleasant-moment-fully',
            'duration' => 660,
            'source_url' => 'https://www.wyevalley.nhs.uk/services/community-services/pain-management-service/mindfulness-mp3-tracks.aspx',
            'title' => [
                'en' => 'Notice a Pleasant Moment Fully',
                'ru' => 'Полностью заметить приятный момент',
            ],
            'description' => [
                'en' => 'Savor a neutral or pleasant experience and feel how it lives in the body. This helps build awareness that is not limited to stress or problems.',
                'ru' => 'Смакуйте нейтральный или приятный опыт и почувствуйте, как он живёт в теле. Это помогает развивать осознавание, которое не сводится только к стрессу и проблемам.',
            ],
        ],
        26 => [
            'slug' => 'mindful-routine-not-mindless-routine',
            'duration' => 300,
            'source_url' => 'https://www.mayoclinic.org/tests-procedures/meditation/in-depth/mindfulness-exercises/art-20046356',
            'title' => [
                'en' => 'Mindful Routine, Not Mindless Routine',
                'ru' => 'Осознанная рутина вместо автоматизма',
            ],
            'description' => [
                'en' => 'Choose one daily activity such as tea, showering, washing hands, or eating and do it with full attention. Everyday life becomes practice when awareness is deliberate.',
                'ru' => 'Выберите одно повседневное действие - чай, душ, мытьё рук или еду - и выполняйте его с полным вниманием. Повседневная жизнь становится практикой, когда осознавание намеренно.',
            ],
        ],
        27 => [
            'slug' => 'longer-pause-in-a-busy-day',
            'duration' => 240,
            'source_url' => 'https://www.sheffieldtalkingtherapies.nhs.uk/first-steps-mindfulness-audio',
            'title' => [
                'en' => 'A Longer Pause in a Busy Day',
                'ru' => 'Более длинная пауза в насыщенном дне',
            ],
            'description' => [
                'en' => 'Use a regular breathing space between tasks to keep stress from stacking up. Short pauses work best when repeated before you are overwhelmed.',
                'ru' => 'Используйте регулярное дыхательное пространство между задачами, чтобы стресс не накапливался. Короткие паузы работают лучше всего, когда вы повторяете их до того, как перегрузка станет сильной.',
            ],
        ],
        28 => [
            'slug' => 'evening-body-scan-for-rest',
            'duration' => 840,
            'source_url' => 'https://www.mindful.org/a-self-guided-day-of-mindfulness/',
            'title' => [
                'en' => 'Evening Body Scan for Rest',
                'ru' => 'Вечернее сканирование тела для отдыха',
            ],
            'description' => [
                'en' => 'Scan the body at the end of the day and allow sensations to be present without changing them. This can lower activation and support sleep readiness.',
                'ru' => 'Сканируйте тело в конце дня и позволяйте ощущениям просто присутствовать, не меняя их. Это помогает снизить внутреннее напряжение и подготовить себя ко сну.',
            ],
        ],
        29 => [
            'slug' => 'build-your-ongoing-practice',
            'duration' => 600,
            'source_url' => 'https://www.mentalhealth.org.uk/explore-mental-health/a-z-topics/mindfulness',
            'title' => [
                'en' => 'Build Your Ongoing Practice',
                'ru' => 'Собрать свою постоянную практику',
            ],
            'description' => [
                'en' => 'Choose the practices that help you most and make them repeatable. A few minutes done consistently is more valuable than occasional intensity.',
                'ru' => 'Выберите практики, которые помогают вам больше всего, и сделайте их повторяемыми. Несколько минут, выполняемых регулярно, ценнее редких всплесков интенсивности.',
            ],
        ],
    ];

    public function run(): void
    {
        $focusProblems = $this->seedTranslatedRecords(FocusProblem::class, self::FOCUS_PROBLEMS);
        $experienceLevels = $this->seedTranslatedRecords(ExperienceLevel::class, self::EXPERIENCE_LEVELS);
        $moduleChoices = $this->seedTranslatedRecords(ModuleChoice::class, self::MODULE_CHOICES);
        $meditationTypes = $this->seedTranslatedRecords(MeditationType::class, self::MEDITATION_TYPES);

        foreach (range(1, count(self::DAY_GUIDES)) as $day) {
            $dayGuide = self::DAY_GUIDES[$day];

            foreach ($focusProblems as $focusProblem) {
                foreach ($experienceLevels as $experienceLevel) {
                    foreach ($moduleChoices as $moduleChoice) {
                        foreach ($meditationTypes as $meditationType) {
                            $this->seedPracticeCombination(
                                day: $day,
                                dayGuide: $dayGuide,
                                focusProblem: $focusProblem,
                                experienceLevel: $experienceLevel,
                                moduleChoice: $moduleChoice,
                                meditationType: $meditationType,
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TModel>  $modelClass
     * @param  array<int, array{en: string, ru: string}>  $titles
     * @return array<int, TModel>
     */
    private function seedTranslatedRecords(string $modelClass, array $titles): array
    {
        return collect($titles)
            ->map(
                fn (array $title) => $modelClass::query()->updateOrCreate(
                    ['title->en' => $title['en']],
                    ['title' => $title],
                ),
            )
            ->all();
    }

    /**
     * @param  array{
     *     slug: string,
     *     duration: int,
     *     source_url: string,
     *     title: array{en: string, ru: string},
     *     description: array{en: string, ru: string}
     * }  $dayGuide
     */
    private function seedPracticeCombination(
        int $day,
        array $dayGuide,
        FocusProblem $focusProblem,
        ExperienceLevel $experienceLevel,
        ModuleChoice $moduleChoice,
        MeditationType $meditationType,
    ): void {
        Practice::query()->updateOrCreate(
            [
                'day' => $day,
                'focus_problem_id' => $focusProblem->getKey(),
                'experience_level_id' => $experienceLevel->getKey(),
                'module_choice_id' => $moduleChoice->getKey(),
                'meditation_type_id' => $meditationType->getKey(),
            ],
            [
                'duration' => $dayGuide['duration'],
                'image_path' => null,
                'video_path' => null,
                'is_active' => true,
                'title' => $this->buildPracticeTitle(
                    $day,
                    $dayGuide,
                    $focusProblem,
                    $experienceLevel,
                    $moduleChoice,
                    $meditationType,
                ),
                'description' => $this->buildPracticeDescription(
                    $dayGuide,
                    $focusProblem,
                    $experienceLevel,
                    $moduleChoice,
                    $meditationType,
                ),
            ],
        );
    }

    /**
     * @param  array{
     *     title: array{en: string, ru: string}
     * }  $dayGuide
     * @return array{en: string, ru: string}
     */
    private function buildPracticeTitle(
        int $day,
        array $dayGuide,
        FocusProblem $focusProblem,
        ExperienceLevel $experienceLevel,
        ModuleChoice $moduleChoice,
        MeditationType $meditationType,
    ): array {
        return [
            'en' => "Day {$day}: {$dayGuide['title']['en']} · {$focusProblem->getTitle('en')} / {$experienceLevel->getTitle('en')} / {$moduleChoice->getTitle('en')} / {$meditationType->getTitle('en')}",
            'ru' => "День {$day}: {$dayGuide['title']['ru']} · {$focusProblem->getTitle('ru')} / {$experienceLevel->getTitle('ru')} / {$moduleChoice->getTitle('ru')} / {$meditationType->getTitle('ru')}",
        ];
    }

    /**
     * @param  array{
     *     description: array{en: string, ru: string}
     * }  $dayGuide
     * @return array{en: string, ru: string}
     */
    private function buildPracticeDescription(
        array $dayGuide,
        FocusProblem $focusProblem,
        ExperienceLevel $experienceLevel,
        ModuleChoice $moduleChoice,
        MeditationType $meditationType,
    ): array {
        return [
            'en' => $dayGuide['description']['en'].' Best for '.$focusProblem->getTitle('en').' with a '.$meditationType->getTitle('en').' practice at the '.$experienceLevel->getTitle('en').' level in the '.$moduleChoice->getTitle('en').' module.',
            'ru' => $dayGuide['description']['ru'].' Подходит для темы "'.$focusProblem->getTitle('ru').'", формата "'.$meditationType->getTitle('ru').'", уровня "'.$experienceLevel->getTitle('ru').'" и модуля "'.$moduleChoice->getTitle('ru').'".',
        ];
    }
}
