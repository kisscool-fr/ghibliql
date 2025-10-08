
| branch | Build status |
| ------ | ------------ |
| master | ![Master build Status](https://github.com/kisscool-fr/ghibliql/actions/workflows/main.yml/badge.svg?branch=master)|
| dev    | ![Dev build Status](https://github.com/kisscool-fr/ghibliql/actions/workflows/main.yml/badge.svg?branch=2.4)|

# GhibliQL

GhibliQL is a [GraphQL](http://graphql.org/) wrapper to the [Studio Ghibli REST API](https://ghibliapi.vercel.app)

## Build & Run

GhibliQL runs locally in Docker, there is a Makefile to help you:

| command      | description                            |
| ------------ | -------------------------------------- |
| `make pull`  | pull `httpd` and `redis` docker images |
| `make build` | build the `php` image                  |
| `make prod`  | download the minimum PHP packages      |
| `make dev`   | download the optional PHP packages     |
| `make run`   | run the server locally, on port 8080   |
| `make audit` | run gitleaks audit (require gitleaks)  |

## Usage

First, you'll need a GraphQL client to query GhibliQL, like [Bruno](https://www.usebruno.com/), [Insomnia](https://insomnia.rest/) or [GraphQL IDE](https://github.com/redound/graphql-ide)

If you use Bruno, you'll find some query example in `docs/bruno/` (in Bruno, `Open Collection` and point to this directory, then choose `dev` environnment)

Then, launch the server with `make run`, then configure your GraphQL client to use the endpoint `http://localhost:8080`

Finally, you'll be able to explore the API using "easy-to-understand" GraphQL query.

For example, if I want the full list of films title, director, and characters name:
```
{
  films {
    title
    director
    people {
      name
    }
  }
}
```

This will output (partial output here):
```
"data": {
  "films": [
    {
      "title": "Castle in the Sky",
      "director": "Hayao Miyazaki",
      "people": [
        {
          "name": "Colonel Muska"
        }
      ]
    },
    {
      "title": "My Neighbor Totoro",
      "director": "Hayao Miyazaki",
      "people": [
        {
          "name": "Satsuki Kusakabe"
        },
        {
          "name": "Mei Kusakabe"
        },
        {
          "name": "Tatsuo Kusakabe"
        },
        {
          "name": "Yasuko Kusakabe"
        },
        {
          "name": "Totoro"
        },
        {
          "name": "Catbus"
        },
        {
          "name": "Granny"
        },
        {
          "name": "Kanta Ogaki"
        }
      ]
    },
  ...
  ]
}
```

If you want to work on a specific object, you'll first need to get his ID:
```
{
  films {
    id
    title
  }
}
```

```
"data": {
  "films": [
    {
      "id": "2baf70d1-42bb-4437-b551-e5fed5a87abe",
      "title": "Castle in the Sky"
    },
    {
      "id": "12cfb892-aac0-4c5b-94af-521852e46d6a",
      "title": "Grave of the Fireflies"
    },
    {
      "id": "58611129-2dbc-4a81-a72f-77ddfc1b1b49",
      "title": "My Neighbor Totoro"
    },
    {
      "id": "ea660b10-85c4-4ae3-8a5f-41cea3648e3e",
      "title": "Kiki's Delivery Service"
    },
  ...
  ]
}
```

Then query with the ID (for example, for "Castle in the Sky"):
```
{
  film(id:"2baf70d1-42bb-4437-b551-e5fed5a87abe") {
    title
    director
  }
}
```
```
{
  "data": {
    "film": {
      "title": "Castle in the Sky",
      "director": "Hayao Miyazaki"
    }
  }
}
```

You've tons of data available, feel free to explore :)

## Limitation

Because it's a wrapper, the data returns are provided by the original API.
Any errors / missing data need to be reported to [GhibliAPI](https://github.com/janaipakos/ghibliapi).

## Roadmap
- Improve performances with better caching

## Credits

Of course, big thanks to [James Anaipakos](https://github.com/janaipakos/ghibliapi) for his awesome work on the REST API !
