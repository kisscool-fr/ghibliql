
[![Build Status](https://github.com/kisscool-fr/ghibliql/actions/workflows/main.yml/badge.svg?branch=master](https://github.com/kisscool-fr/ghibliql)

# GhibliQL

GhibliQL is a [GraphQL](http://graphql.org/) wrapper to the [Studio Ghibli REST API](https://ghibliapi.vercel.app)

## Build & run

GhibliQL runs locally in Docker.

1. `make pull`: pull httpd and redis docker images
2. `make build`: build the php image
3. `make prod`: download the minimum PHP packages
4. `make run`: run the server locally

## Usage

First, you'll need a GraphQL client to query GhibliQL, like [Insomnia](https://insomnia.rest/) or [GraphQL IDE](https://github.com/redound/graphql-ide)

, you just have to build & launch with `make run`, then configure your GraphQL client to use the endpoint http://localhost:8080

Then, you'll be able to explore the API using "easy-to-understand" GraphQL query.

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

## Credits

Of course, big thanks to [James Anaipakos](https://github.com/janaipakos/ghibliapi) for his awesome work on the REST API !
